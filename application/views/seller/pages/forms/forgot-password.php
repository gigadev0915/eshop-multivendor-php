<body class="hold-transition login-page">
<?php 
$auth_settings = get_settings('authentication_settings', true);
?> 
  <div class="login-box">
    <!-- /.login-logo -->
    <input type="hidden" id="auth_settings" name="auth_settings" value='<?= isset($auth_settings['authentication_method']) ? $auth_settings['authentication_method'] : ''; ?>'>
    <div class="card">
      <div class="card-body login-card-body">
        <div class="login-logo">
          <a href="<?= base_url('admin') ?>"><img src="<?= base_url() . $logo ?>"></a>
        </div>
        <div class="text-center h5"><?= !empty($this->lang->line('forgot_password')) ? $this->lang->line('forgot_password') : 'Forgot Password' ?></div>
        <hr class="mt-0">
        <form id="send_forgot_password_otp_form" method="POST" action="#">
        <input type="hidden" name="forget_password_val" value="1" id="forget_password_val">
          <div class="col-md-12">
            <input type="text" class="form-control" name="mobile_number" id="forgot_password_number" placeholder="Mobile number" value="">
          </div>
          <div class="col-md-12 d-flex justify-content-center pb-4 mt-3">
            <div id="recaptcha-container-2"></div>
          </div>
          <footer>
            <button type="submit" id="forgot_password_send_otp_btn" class="submit_btn  btn btn-primary btn-block forgot-send-otp-btn"><?= !empty($this->lang->line('send_otp')) ? $this->lang->line('send_otp') : 'Send OTP' ?></button>
          </footer>
          <br>
          <p class="mt-3 mb-1">
            <a href="<?= base_url('seller/home/') ?>">Login</a>
          </p>
          <div class="d-flex justify-content-center">
            <div class="form-group" id="forgot_pass_error_box"></div>
          </div>
        </form>
        <form id="verify_forgot_password_otp_form" class="d-none" method="post" action="#">
          <div class="col-md-12 mb-2">
            <input type="text" id="forgot_password_otp" class="form-control" name="otp" placeholder="OTP" value="" autocomplete="off" required>
          </div>
          <div class="col-md-12 mb-4 mt-2">
            <input type="password" class="form-control" name="new_password" placeholder="New Password" value="" required>
          </div>
          <footer>
            <button type="submit" class="submit_btn mt-2 btn btn-primary btn-block" id="reset_password_submit_btn"><?= !empty($this->lang->line('submit')) ? $this->lang->line('submit') : 'Submit' ?></button>
          </footer>
          <br>
          
          <div class="d-flex justify-content-center">
            <div class="form-group" id="set_password_error_box"></div>
          </div>
        </form>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>