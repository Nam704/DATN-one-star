<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $orderDetails; // Đổi từ $order thành $orderDetails để lưu dữ liệu từ detailsOrder()

    public function __construct(array $orderDetails)
    {
        $this->orderDetails = $orderDetails;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Đặt hàng thành công #' . $this->orderDetails['code'], // Dùng code từ detailsOrder()
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_placed', // Đổi tên view cho phù hợp, không dùng admin.mail.order-placed
            with: ['orderDetails' => $this->orderDetails] // Truyền dữ liệu vào view
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
