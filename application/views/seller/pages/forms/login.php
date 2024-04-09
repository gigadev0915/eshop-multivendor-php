<?php if (ALLOW_MODIFICATION == 0) { ?>
    <div class="alert alert-warning">
        Note: If you cannot login here, please close the codecanyon frame by clicking on x Remove Frame button from top right corner on the page or <a href="<?= base_url('/admin') ?>" target="_blank" class="text-danger">>> Click here << </a>
    </div>
<?php } ?>
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id='has_account_model'>
    <div class="modal-dialog  edit-modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verify User Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('seller/auth/verify-account') ?>" id="verify-acount-form" class='form-submit-event' method="post">

                    <div class="input-group mb-3">
                        <input type='hidden' name='<?= $this->security->get_csrf_token_name() ?>' value='<?= $this->security->get_csrf_hash() ?>'>
                        <input type="<?= $identity_column ?>" class="form-control" name="identity" placeholder="<?= ucfirst($identity_column)  ?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas <?= ($identity_column == 'email') ? 'fa-envelope' : 'fa-mobile' ?> "></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" id="submit_btn1" class="btn btn-primary btn-block">Verify Account</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="login-box">

    <!-- /.login-logo -->
    <div class="container-fluid ">
        <div class="card-body login-card-body">
            <div class="login-logo">
                <a href="<?= base_url() . 'seller/login' ?>"><img src="<?= base_url() . $logo ?>"></a>
            </div>
            <div class="row">
                <h2 class="text-dark">Welcome Back!</h2>
                <p class="text-dark mb-4">Please login to your account</p>
                <!-- <p class="login-box-msg">Sign in to start your session</p> -->
            </div>
            <form action="<?= base_url('auth/login') ?>" class='form-submit-event' method="post">
                <div class="mb-3">
                    <input type='hidden' name='<?= $this->security->get_csrf_token_name() ?>' value='<?= $this->security->get_csrf_hash() ?>'>
                    <label for="mobile" class="form-label text-dark">Mobile </label>
                    <input type="<?= $identity_column ?>" class="form-control form-input" name="identity" id="mobile" placeholder="Enter Your <?= ucfirst($identity_column)  ?>" value="<?= (ALLOW_MODIFICATION == 0) ? '9988776655' : '' ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label text-dark">Password</label>
                    <input type="password" class="form-control form-input" name="password" id="password" placeholder="Enter Your Password" value="<?= (ALLOW_MODIFICATION == 0) ? '12345678' : '' ?>">

                </div>

                <div class="row">
                    <div class="col-12 mb-3 text-right">
                        <a href="<?= base_url('/seller/login/forgot_password') ?>" class="text-dark"><?= !empty($this->lang->line('forgot_password')) ? $this->lang->line('forgot_password') : 'Forgot Password' ?> ?</a>
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
            <div class="mb-3">
                <a href="#" class="text text-secondary font-weight-bold mb-3" id="has_account" data-target="#has_account_model" data-toggle="modal">Already have user account with <span class="text text-primary"><?= $app_name ?>?</span></a>
            </div>
            <div>
                <a href="<?= base_url('seller/auth/sign_up') ?>" class="text text-danger font-weight-bold">Don't have any account?</a>
            </div>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->