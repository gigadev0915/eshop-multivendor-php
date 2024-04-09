<?php if (ALLOW_MODIFICATION == 0) { ?>
    <div class="alert alert-warning">
        Note: If you cannot login here, please close the codecanyon frame by clicking on x Remove Frame button from top right corner on the page or <a href="<?= base_url('/admin') ?>" target="_blank" class="text-danger">>> Click here << </a>
    </div>
<?php } ?>
<div class="login-box">
    <!-- /.login-logo -->
    <div class="container-fluid ">
        <div class="card-body login-card-body">
            <div class="login-logo">
                <a href="<?= base_url() . 'admin/login' ?>"><img src="<?= get_image_url($logo, 'thumb', 'sm'); ?>"></a>
            </div>
            <h2 class="text-dark">Welcome Back!</h2>
            <p class="text-dark mb-4">Please login to your account</p>

            <form action="<?= base_url('auth/login') ?>" class='form-submit-event' method="post">
                <input type='hidden' name='<?= $this->security->get_csrf_token_name() ?>' value='<?= $this->security->get_csrf_hash() ?>'>
                <div class="mb-3">
                    <label for="mobile" class="form-label text-dark">Mobile </label>
                    <input type="<?= $identity_column ?>" class="form-control form-input" name="identity" id="mobile" placeholder="Enter Your <?= ucfirst($identity_column)  ?>" value="<?= (ALLOW_MODIFICATION == 0) ? '9876543210' : '' ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label text-dark">Password</label>
                    <input type="password" class="form-control form-input" name="password" id="password" placeholder="Enter Your Password" value="<?= (ALLOW_MODIFICATION == 0) ? '12345678' : '' ?>">
                </div>
                <div class="row">
                    <div class="col-12 mb-3 text-right">
                        <a href="<?= base_url('/admin/login/forgot_password') ?>" class="text-dark"><?= !empty($this->lang->line('forgot_password')) ? $this->lang->line('forgot_password') : 'Forgot Password' ?> ?</a>
                    </div>
                    <div class="col-8 mb-4">
                        <div class="check-primary">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember" class="form-check-label">
                                Remember Me
                            </label>
                        </div>
                    </div>

                    <!-- /.col -->
                    <div class="col-12">
                        <button type="submit" id="submit_btn" class="btn btn-block p-2 btn-signin">Sign In</button>
                    </div>

                </div>
            </form>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->