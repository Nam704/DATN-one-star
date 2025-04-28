<?php

namespace App\Services;

use App\Events\AdminNotification;
use App\Events\EmployeeNotification;
use App\Events\PrivateNotification;
use App\Events\PublicNotification;
use App\Events\UserNotification;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NotificationService
{
    // Ánh xạ category với route mặc định
    protected $routeMap = [
        'payment' => 'client.user.myAccount',
        'order' => 'client.order.detail',
        'promotion' => 'client.promotions',
        'system' => 'client.dashboard',
    ];

    public function __construct()
    {
        // Constructor logic (nếu cần)
    }

    /**
     * Validate dữ liệu thông báo trước khi xử lý
     */
    protected function validateNotificationData(array $data, array $additionalRules = []): void
    {
        $rules = array_merge([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'from_user_id' => 'nullable|integer|exists:users,id',
            'to_user_id' => 'nullable|integer|exists:users,id',
            'type' => 'required|string',
            'category' => 'nullable|string|in:payment,order,promotion,system',
            'priority' => 'nullable|string|in:high,medium,low',
            // 'status' => 'required|string|in:unread,read',
            'goto_id' => 'nullable',
            'goto_route' => 'nullable|string',
            'expires_at' => 'nullable|date',
        ], $additionalRules);

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            throw new \InvalidArgumentException('Dữ liệu thông báo không hợp lệ: ' . $validator->errors()->first());
        }
    }

    /**
     * Gửi thông báo công khai
     */
    public function sendPublic(array $data): void
    {
        try {
            $this->validateNotificationData($data);
            $data = $this->prepareNotificationData($data, 'public');
            SendNotificationJob::dispatch([
                'type' => 'public',
                'title' => $data['title'],
                'message' => $data['message'],
                'category' => $data['category'] ?? 'system',
                'priority' => $data['priority'] ?? 'medium',
                'from_user_id' => $data['from_user_id'] ?? null,
                'to_user_id' => null,
                'status' => $data['status'] ?? 'unread',
                'goto_id' => $data['goto_id'] ?? null,
                'goto_route' => $data['goto_route'] ?? $this->routeMap[$data['category'] ?? 'system'] ?? 'client.dashboard',
                'expires_at' => $data['expires_at'] ?? now()->addDays(7),
            ]);
            Log::info('Đã xếp hàng thông báo công khai', ['title' => $data['title']]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi thông báo công khai: ' . $e->getMessage(), [
                'data' => $data,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Chuẩn bị dữ liệu thông báo với các giá trị mặc định
     */
    protected function prepareNotificationData(array $data, string $type): array
    {
        return [
            'type' => $data['type'] ?? $type,
            'category' => $data['category'] ?? 'system',
            'priority' => $data['priority'] ?? 'medium',
            'title' => $data['title'],
            'message' => $data['message'],
            'from_user_id' => $data['from_user_id'] ?? null,
            'to_user_id' => $data['to_user_id'] ?? null,
            'status' => $data['status'] ?? 'unread',
            'goto_id' => $data['goto_id'] ?? null,
            'goto_route' => $data['goto_route'] ?? ($this->routeMap[$data['category'] ?? 'system'] ?? 'client.dashboard'),
            'expires_at' => $data['expires_at'] ?? now()->addDays(7),
        ];
    }

    /**
     * Gửi thông báo đến một vai trò cụ thể
     */
    private function sendToRole(array $data, string $role, ?int $specificUserId = null): void
    {
        try {
            $this->validateNotificationData($data);
            $query = User::whereHas('role', fn($q) => $q->where('name', $role))
                ->where('id', '!=', $data['from_user_id'] ?? 0);
            if ($specificUserId) {
                $query->where('id', $specificUserId);
            }
            $recipients = $query->pluck('id');

            if ($role === 'admin' && !$specificUserId && isset($data['from_user_id'])) {
                $sender = User::find($data['from_user_id']);
                if ($sender && $sender->role->name === 'admin') {
                    $recipients->push($sender->id);
                }
            }

            $data = $this->prepareNotificationData($data, $role);
            foreach ($recipients as $userId) {
                SendNotificationJob::dispatch([
                    'type' => $data['type'],
                    'category' => $data['category'],
                    'priority' => $data['priority'],
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'from_user_id' => $data['from_user_id'],
                    'to_user_id' => $userId,
                    'status' => $data['status'],
                    'goto_id' => $data['goto_id'],
                    'goto_route' => $data['goto_route'],
                    'expires_at' => $data['expires_at'],
                ]);
            }

            $eventClass = match ($role) {
                'user' => UserNotification::class,
                'employee' => EmployeeNotification::class,
                'admin' => AdminNotification::class,
                default => throw new \InvalidArgumentException("Vai trò không hợp lệ: $role"),
            };
            broadcast(new $eventClass($data))->toOthers();

            Log::info("Đã xếp hàng thông báo đến $role", [
                'title' => $data['title'],
                'recipients' => $recipients->toArray(),
            ]);
        } catch (\Exception $e) {
            Log::error("Lỗi khi gửi thông báo đến $role: " . $e->getMessage(), [
                'data' => $data,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function sendUser(array $data, ?int $specificUserId = null): void
    {
        $this->sendToRole($data, 'user', $specificUserId);
    }

    public function sendEmployee(array $data, ?int $specificUserId = null): void
    {
        $this->sendToRole($data, 'employee', $specificUserId);
    }

    public function sendAdmin(array $data, ?int $specificUserId = null): void
    {
        $this->sendToRole($data, 'admin', $specificUserId);
    }

    /**
     * Gửi thông báo riêng tư đến một người dùng cụ thể
     */
    public function sendPrivate(array $data): void
    {
        try {
            $this->validateNotificationData($data, ['to_user_id' => 'required|integer|exists:users,id']);
            $data = $this->prepareNotificationData($data, 'private');
            SendNotificationJob::dispatch([
                'type' => $data['type'],
                'category' => $data['category'],
                'priority' => $data['priority'],
                'title' => $data['title'],
                'message' => $data['message'],
                'from_user_id' => $data['from_user_id'],
                'to_user_id' => $data['to_user_id'],
                'status' => $data['status'],
                'goto_id' => $data['goto_id'],
                'goto_route' => $data['goto_route'],
                'expires_at' => $data['expires_at'],
            ]);
            Log::info('Đã xếp hàng thông báo riêng tư', [
                'title' => $data['title'],
                'to_user_id' => $data['to_user_id'],
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi thông báo riêng tư: ' . $e->getMessage(), [
                'data' => $data,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Tạo bản ghi thông báo trong cơ sở dữ liệu
     */
    public function createNotification(array $data): Notification
    {
        return Notification::create([
            'type' => $data['type'],
            'category' => $data['category'] ?? 'system',
            'priority' => $data['priority'] ?? 'medium',
            'title' => $data['title'],
            'message' => $data['message'],
            'from_user_id' => $data['from_user_id'] ?? null,
            'to_user_id' => $data['to_user_id'],
            'status' => $data['status'] ?? 'unread',
            'goto_id' => $data['goto_id'] ?? null,
            'goto_route' => $data['goto_route'] ?? ($this->routeMap[$data['category'] ?? 'system'] ?? 'client.dashboard'),
            'expires_at' => $data['expires_at'] ?? now()->addDays(7),
        ]);
    }

    /**
     * Đánh dấu thông báo là đã đọc
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        try {
            $notification = Notification::where('id', $notificationId)
                ->where('to_user_id', $userId)
                ->firstOrFail();

            if ($notification->status === 'read') {
                return true;
            }

            $notification->status = 'read';
            $notification->read_at = now();
            return $notification->save();
        } catch (\Exception $e) {
            Log::error('Lỗi khi đánh dấu thông báo đã đọc: ' . $e->getMessage(), [
                'notification_id' => $notificationId,
                'user_id' => $userId,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Đánh dấu tất cả thông báo của người dùng là đã đọc
     */
    public function markAllAsRead(int $userId): bool
    {
        try {
            return Notification::where('to_user_id', $userId)
                ->where('status', 'unread')
                ->update([
                    'status' => 'read',
                    'read_at' => now(),
                ]) >= 0;
        } catch (\Exception $e) {
            Log::error('Lỗi khi đánh dấu tất cả thông báo đã đọc: ' . $e->getMessage(), [
                'user_id' => $userId,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Lấy tất cả thông báo của một người dùng với phân trang
     */
    public function getNotificationsByUser(int $userId, int $perPage = 15, ?string $category = null, ?string $priority = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Notification::where('to_user_id', $userId)
            ->whereNull('expires_at')
            ->orWhere('expires_at', '>', now());

        if ($category) {
            $query->where('category', $category);
        }
        if ($priority) {
            $query->where('priority', $priority);
        }

        return $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Lấy thông báo chưa đọc của một người dùng với phân trang
     */
    public function getUnreadNotifications(int $userId, int $perPage = 15, ?string $category = null, ?string $priority = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Notification::where('to_user_id', $userId)
            ->where('status', 'unread')
            ->whereNull('expires_at')
            ->orWhere('expires_at', '>', now());

        if ($category) {
            $query->where('category', $category);
        }
        if ($priority) {
            $query->where('priority', $priority);
        }

        return $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Xóa thông báo
     */
    public function deleteNotification(int $notificationId): bool
    {
        try {
            $notification = Notification::findOrFail($notificationId);
            return $notification->delete();
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa thông báo: ' . $e->getMessage(), [
                'notification_id' => $notificationId,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}