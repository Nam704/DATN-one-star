<div class="tab-pane fade" id="account-details">
    <h3>Quản lý thông tin tài khoản</h3>
    <div class="login">
        <div class="login_form_container">
            <div class="account_login_form">
                <form action="#" class="form-details">
                    <div class="row row-cols-sm-2 row-cols-1">
                        <div class="mb-2">
                            <input type="hidden" value="{{ $user->id }}" id="user_id">
                            <label class="form-label" for="FullName">Tên người dùng</label>
                            <input type="text" name="name" value="{{ $user->name ?? '' }}" id="FullName"
                                class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="Email">Email</label>
                            <input type="email" value="{{ $user->email ?? '' }}" name="email" id="Email"
                                class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="web-url">Mật khẩu cũ</label>
                            <input type="text" value="" name="old_password"
                                placeholder="Nhập mật khẩu cũ" id="web-url" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="phone">Số điện thoại</label>
                            <input type="text" value="{{ $user->phone ?? '' }}" name="phone" id="phone"
                                placeholder="Nhập số điện thoại" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="new_password"> Mật khẩu mới</label>
                            <input type="password" placeholder="Mật khẩu dài 8-10 kí tự" id="new_password"
                                class="form-control" name="new_password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="new_password_confirmation">Nhập laị mật khẩu</label>
                            <input type="password" placeholder="Nhập trùng mật khẩu mới" name="new_password_confirmation"
                                id="new_password_confirmation" class="form-control">
                        </div>

                    </div>
                    <button class="btn btn-primary" type="submit"><i class="ri-save-line me-1 fs-16 lh-1"></i>
                        Lưu thông tin</button>
                </form>
            </div>
        </div>
    </div>
</div>
