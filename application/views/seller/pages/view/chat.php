<input type="hidden" id="firebase_settings" value='<?= $firebase_settings ?>'>
<div class="content-wrapper">
    <div class="main-wrapper main-wrapper-1">
        <section class="content">
            <div class="container-fluid">
                <div id="app">
                    <div class="section-body chat-theme">
                        <div class="row justify-content-center unselectable no-gutters">
                            <div class="navbar-nav nav__list js-nav__list col-12 col-sm-4 col-lg-3">
                                <div class="card chat-theme-light chat-scroll chat-min">
                                    <select name="select_user_id[]" id="chat_user" class="search_user w-100" multiple data-placeholder=" Type to search and select users" onload="multiselect()">
                                        <?php
                                        $user_details = fetch_details('users', ['active' => 1],);
                                        if (!empty($user_details)) {
                                        ?>
                                            <option value="<?= $user_details[0]['id'] ?>"> <?= $user_details[0]['opponent_username'] ?></option>
                                        <?php
                                        }

                                        ?>
                                    </select>
                                    <div id="add-scroll-js">
                                        <div class="card-header chat-card-header text-color">
                                            <h4>Personal Chat</h4>
                                        </div>
                                        <div class="chat-card-body">
                                            <ul class="list-unstyled list-unstyled-border chat-list-unstyled-border">

                                                <?php if (!empty($users)) {
                                                    foreach ($users as $user) {
                                                        if ($user['opponent_user_id'] == $_SESSION['user_id']) { ?>
                                                            <li class="media">
                                                                <div class="media-body">
                                                                    <div class="chat-person" data-picture="" data-type="person" data-id="<?= $user['opponent_user_id'] ?>"><i class="<?= ($user['is_online'] == 1) ? 'fas fa-circle text-success' : 'far fa-circle'; ?> "></i> <?= $user['opponent_username'] ?> (You)</div>
                                                                </div>
                                                            </li>
                                                <?php }
                                                    }
                                                } ?>


                                                <?php if (!empty($users)) {
                                                    foreach ($users as $user) {
                                                        if (isset($user['opponent_user_id']) && !empty($user['opponent_user_id']) && $user['opponent_user_id'] != '' && $user['opponent_user_id'] != $_SESSION['user_id']) {
                                                ?>
                                                            <li class="media">
                                                                <div class="media-body">
                                                                    <div data-unread_msg="<?= $user['unread_msg'] ?>" class="chat-person <?= ($user['unread_msg'] > 0) ? 'new-msg-rcv' : ''; ?>" data-picture="<?= $user['picture'] ?>" data-type="person" data-id="<?= $user['opponent_user_id'] ?>"><i class="<?= ($user['is_online'] == 1) ? 'fas fa-circle text-success' : 'far fa-circle'; ?> "></i> <?= $user['opponent_username'] ?>
                                                                        <?= ($user['unread_msg'] > 0) ? (($user['unread_msg'] > 9) ? '<div class="badge-chat">9 +</div>' : '<div class="badge-chat">' . $user['unread_msg'] . '</div>') : ''; ?>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                <?php }
                                                    }
                                                } ?>

                                            </ul>


                                            </ul>
                                        </div>
                                        <div class="card-header chat-card-header d-flex text-color">
                                            <h4><?= !empty($this->lang->line('support_chat')) ? $this->lang->line('support_chat') : 'Support Team '; ?></h4>
                                        </div>
                                        <div class="chat-card-body">
                                            <ul class="list-unstyled list-unstyled-border chat-list-unstyled-border">

                                                <?php if (!empty($supporters)) {

                                                    foreach ($supporters as $supporter) {
                                                        $date = strtotime('now');
                                                        $to_id = $this->session->userdata('user_id');

                                                        if ($to_id == $supporter['user_permission_id']) {
                                                            $supporter['is_online'] = 1;
                                                        } else {
                                                            if ($supporter['last_online'] > $date) {
                                                                $supporter['is_online'] = 1;
                                                            } else {
                                                                $supporter['is_online'] = 0;
                                                            }
                                                        }
                                                        // echo "<pre>";
                                                        // print_r($supporter);
                                                ?>

                                                        <li class="media">
                                                            <div class="media-body">
                                                                <div class="chat-person" data-id="<?= $supporter['userto_id'] ?>" data-type="person">
                                                                    <i class="<?= ($supporter['is_online'] == 1) ? 'fas fa-circle text-success' : 'far fa-circle'; ?> "></i> <?= $supporter['username'] ?>
                                                                </div>
                                                            </div>
                                                        </li>

                                                <?php }
                                                } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-8 col-lg-9" id="chat_area_wait">
                            </div>
                            <!-- <div class="col-12 col-sm-8 col-lg-9 d-none" id="you_not_in_group">

                                <div class="pricing pricing-highlight chat-min">
                                    <div class="pricing-padding">
                                        <div class="pricing-price">
                                            <div>You are not in this group</div>
                                            <div>Join group and access all the details.</div>
                                        </div>
                                        <a class="btn btn-success text-white" id="you_not_in_group_btn">Join Group</a>
                                    </div>
                                </div>

                            </div> -->
                            <div class="col-12 col-sm-8 col-lg-9 d-none" id="chat_area">
                                <div class="card chat-box chat-theme-light chat-min " id="mychatbox2">
                                    <div class="card-header chat-card-header d-flex">
                                        <div class="mr-3" id="chat-avtar-main">#</div>
                                        <div class="media-body">
                                            <div class="mt-0 mb-1 font-weight-bold text-color" id="chat_title"></div>
                                            <div class="text-small font-600-bold" id="chat_online_status"></div>
                                        </div>

                                    </div>
                                    <div id="chat-box-content" class="chat-bg card-body chat-scroll chat-content">
                                        <div class="chat_loader">Loading...</div>
                                    </div>
                                    <div class="card-body d-none" id="chat-dropbox">
                                        <div class="dropzone " id="myAlbum"></div>
                                        <div class="text-center mt-3">
                                            <button class="btn btn-danger shadow-none" onclick="closeDropZone();"><?= !empty($this->lang->line('label_close')) ? $this->lang->line('label_close') : 'Close'; ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-control theme-inputs d-none" id="chat-input-textarea-result"></div>
                                    <div class="card-footer chat-form">
                                        <form id="chat-form2" autocomplete="off">
                                            <div class="row">
                                                <div class="input-group">
                                                    <input type="hidden" id="opposite_user_id" name="opposite_user_id" value="">
                                                    <input type="hidden" id="my_user_id" name="my_user_id" value="<?= $_SESSION['user_id'] ?>" data-picture="">
                                                    <input type="hidden" id="chat_type" name="chat_type" value="">
                                                    <textarea class="form-control theme-inputs" id="chat-input-textarea" rows="1" name="chat-input-textarea"></textarea>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <span class="input-group-append ">
                                                    <div class="form-group">

                                                        <!-- <a Class="chat-preview-btn d-none" id="chat-preview-btn"><?= !empty($this->lang->line('label_preview')) ? $this->lang->line('label_preview') : 'Preview'; ?></a> -->

                                                        <a class="bg-success go-to-bottom-btn text-center">
                                                            <i class="fas fa-arrow-down"></i>
                                                        </a>

                                                        <button class="btn btn-danger btn-send-msg">
                                                            <i class="far fa-paper-plane"></i>
                                                        </button>

                                                        <button class="btn-file btn btn-primary" onclick="showDropZone();">
                                                            <i class="fas fa-paperclip"></i>
                                                        </button>
                                                    </div>
                                                </span>
                                            </div>

                                        </form>
                                    </div>
                                    <!-- <div class="card-footer chat-form">
                                        <form id="chat-form2" autocomplete="off">
                                            <div class="row">
                                                <div class="input-group">
                                                    <input type="hidden" id="opposite_user_id" name="opposite_user_id" value="">
                                                    <input type="hidden" id="my_user_id" name="my_user_id" value="<? //= $_SESSION['user_id'] 
                                                                                                                    ?>">
                                                    <input type="hidden" id="chat_type" name="chat_type" value="">
                                                    <textarea class="form-control theme-inputs" id="chat-input-textarea" rows="1" name="chat-input-textarea"></textarea>
                                                    <a Class="chat-preview-btn d-none" id="chat-preview-btn"><? //= !empty($this->lang->line('label_preview')) ? $this->lang->line('label_preview') : 'Preview'; 
                                                                                                                ?></a>
                                                    <a class="bg-success go-to-bottom-btn">
                                                        <i class="fas fa-arrow-down"></i>
                                                    </a>
                                                </div>

                                        
                                                <div class="form-group">
                                                    <button class="btn btn-danger">
                                                        <i class="far fa-paper-plane"></i>
                                                    </button>
                                                    <button class="btn-file uploadFile img btn btn-primary text-white" data-input='attachments[]' data-isremovable='1' data-is-multiple-uploads-allowed='1' data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo">
                                                        <i class="fas fa-paperclip"></i>
                                                    </button>
                                                    <div class="container-fluid row image-upload-section">
                                                    </div>
                                                </div>
                                            
                                        </form>
                                    </div> -->


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div><!-- /.container-fluid -->

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('<?= base_url('/firebase-messaging-sw.js'); ?>')
            .then(function(registration) {
                console.log('Service Worker registered with scope:', registration.scope);
            })
            .catch(function(error) {
                console.error('Service Worker registration failed:', error);
            });
    }
</script>

<script defer type="text/babel" src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js"></script>
<script defer type="text/babel" src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js"></script>
<script defer type="text/babel" src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging.js"></script>
<!-- chat -->
<script type="module" src="<?= base_url('assets/seller/components-chat-box.js') ?>"></script>