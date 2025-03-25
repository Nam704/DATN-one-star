<div class="tab-pane fade" id="account-details">
    <h3>Thông tin tài khoản </h3>
    @auth
        <div class="login">
            <div class="login_form_container">
                <div class="account_login_form">
                    <form action="{{ route('client.my-account.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <label>Tên người dùng</label>
                        <input type="text" name="name" value="{{ $user->name }}">
                        <label>Email</label>
                        <input type="text" name="email" value="{{ $user->email }}">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" value="{{ $user->phone }}">

                        <div class="save_button primary_btn default_button">
                            <button type="submit">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endauth

    @guest
        <p>Bạn chưa đăng nhập.</p>
        <a href="{{ route('auth.login') }}">Đăng nhập</a>
    @endguest


</div>
