<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Authentication Mode</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Authentication Mode</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <form class="form-horizontal form-submit-event" action="<?= base_url('admin/Authentication_settings/update_authentication_settings'); ?>" method="POST" id="payment_setting_form" enctype="multipart/form-data">
                            <div class="card-body">

                                <input type="hidden" id="auth_setting" name="firebase_config" required="" value='<?= json_encode($authentication_config) ?>' aria-required="true">
                                <!-- <input type="hidden" id="firebase_config" name="firebase_config" required="" value="1" aria-required="true">
                                <input type="hidden" id="smsgateway_config" name="smsgateway_config" required="" value="1" aria-required="true"> -->

                                <div class="">
                                    <div class="form-group d-flex">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="authentication_method" value="firebase" id="firebaseRadio" <?= (@$authentication_config['authentication_method']) == 'firebase' ? 'checked' : '' ?>>
                                            <label class="" for="firebaseRadio">
                                                Firebase Authentication
                                            </label>
                                        </div>
                                        <div class="form-check ml-5">
                                            <input class="form-check-input" type="radio" name="authentication_method" value="sms" id="smsRadio" <?= (@$authentication_config['authentication_method']) == 'sms' ? 'checked' : '' ?>>
                                            <label class="" for="smsRadio">
                                                Custom SMS Gateway OTP based
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="firebase_config d-none">
                                        <a href="<?= base_url('admin/web-setting/firebase') ?>">
                                            <p style="color: red;">Please config firebase config here *</p>
                                        </a>
                                    </div>
                                    <div class="sms_gateway d-none">
                                        <a href="<?= base_url('admin/sms-gateway-settings') ?>">
                                            <p style="color: red;">Please config SMS gateway config here * </p>
                                        </a>
                                    </div>
                                </div>
                                <!-- <div class="d-flex justify-content-center">
                                    <div class="form-group" id="error_box">
                                        <div class="card text-white d-none mb-3">
                                            <div class="card-header"></div>
                                            <div class="card-body"></div>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="form-group">
                                    <button type="reset" class="btn btn-warning">Reset</button>
                                    <button type="submit" class="btn btn-success" id="submit_btn">Update Authentication Settings</button>
                                </div>


                            </div>
                        </form>
                    </div>
                    <!--/.card-->
                </div>
                <!--/.col-md-12-->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>