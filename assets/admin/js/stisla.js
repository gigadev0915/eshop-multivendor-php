"use strict";

(function ($, window, i) {
    // Bootstrap 4 Modal
    $.fn.fireModal = function (options) {
        var options = $.extend({
            size: 'modal-md',
            center: false,
            animation: true,
            title: 'Modal Title',
            closeButton: true,
            header: true,
            bodyClass: '',
            footerClass: '',
            body: '',
            buttons: [],
            autoFocus: true,
            created: function () { },
            appended: function () { },
            onFormSubmit: function () { },
            modal: {}
        }, options);

        this.each(function () {
            i++;
            var id = 'fire-modal-' + i + 1,
                trigger_class = 'trigger--' + id,
                trigger_button = $('.' + trigger_class);

            $(this).addClass(trigger_class);

            // Get modal body
            let body = options.body;

            if (typeof body == 'object') {
                if (body.length) {
                    let part = body;
                    body = body.removeAttr('id').clone().removeClass('modal-part');
                    part.remove();
                } else {
                    body = '<div class="text-danger">Modal part element not found!</div>';
                }
            }

            // Modal base template
            var modal_template = '   <div class="modal' + (options.animation == true ? ' fade' : '') + '" role="dialog" id="' + id + '">  ' +
                '     <div class="modal-dialog ' + options.size + (options.center ? ' modal-dialog-centered' : '') + '" role="document">  ' +
                '       <div class="modal-content">  ' +
                ((options.header == true) ?
                    '         <div class="modal-header">  ' +
                    '           <h5 class="modal-title">' + options.title + '</h5>  ' +
                    ((options.closeButton == true) ?
                        '           <button type="button" class="close" data-dismiss="modal" aria-label="Close">  ' +
                        '             <span aria-hidden="true">&times;</span>  ' +
                        '           </button>  '
                        : '') +
                    '         </div>  '
                    : '') +
                '         <div class="modal-body">  ' +
                '         </div>  ' +
                (options.buttons.length > 0 ?
                    '         <div class="modal-footer">  ' +
                    '         </div>  '
                    : '') +
                '       </div>  ' +
                '     </div>  ' +
                '  </div>  ';

            // Convert modal to object
            var modal_template = $(modal_template);

            // Start creating buttons from 'buttons' option
            var this_button;
            options.buttons.forEach(function (item) {
                // get option 'id'
                let id = "id" in item ? item.id : '';

                // Button template
                this_button = '<button type="' + ("submit" in item && item.submit == true ? 'submit' : 'button') + '" class="' + item.class + '" id="' + id + '">' + item.text + '</button>';

                // add click event to the button
                this_button = $(this_button).off('click').on("click", function () {
                    // execute function from 'handler' option
                    item.handler.call(this, modal_template);
                });
                // append generated buttons to the modal footer
                $(modal_template).find('.modal-footer').append(this_button);
            });

            // append a given body to the modal
            $(modal_template).find('.modal-body').append(body);

            // add additional body class
            if (options.bodyClass) $(modal_template).find('.modal-body').addClass(options.bodyClass);

            // add footer body class
            if (options.footerClass) $(modal_template).find('.modal-footer').addClass(options.footerClass);

            // execute 'created' callback
            options.created.call(this, modal_template, options);

            // modal form and submit form button
            let modal_form = $(modal_template).find('.modal-body form'),
                form_submit_btn = modal_template.find('button[type=submit]');

            // append generated modal to the body
            $("body").append(modal_template);

            // execute 'appended' callback
            options.appended.call(this, $('#' + id), modal_form, options);

            // if modal contains form elements
            if (modal_form.length) {
                // if `autoFocus` option is true
                if (options.autoFocus) {
                    // when modal is shown
                    $(modal_template).on('shown.bs.modal', function () {
                        // if type of `autoFocus` option is `boolean`
                        if (typeof options.autoFocus == 'boolean')
                            modal_form.find('input:eq(0)').focus(); // the first input element will be focused
                        // if type of `autoFocus` option is `string` and `autoFocus` option is an HTML element
                        else if (typeof options.autoFocus == 'string' && modal_form.find(options.autoFocus).length)
                            modal_form.find(options.autoFocus).focus(); // find elements and focus on that
                    });
                }

                // form object
                let form_object = {
                    startProgress: function () {
                        modal_template.addClass('modal-progress');
                    },
                    stopProgress: function () {
                        modal_template.removeClass('modal-progress');
                    }
                };

                // if form is not contains button element
                if (!modal_form.find('button').length) $(modal_form).append('<button class="d-none" id="' + id + '-submit"></button>');

                // add click event
                form_submit_btn.on('click', function () {
                    modal_form.submit();
                });

                // add submit event
                modal_form.on('submit', function (e) {
                    // start form progress
                    form_object.startProgress();

                    // execute `onFormSubmit` callback
                    options.onFormSubmit.call(this, modal_template, e, form_object);
                });
            }

            $(document).on("click", '.' + trigger_class, function () {
                $('#' + id).modal(options.modal);

                return false;
            });
        });
    }

    // Bootstrap Modal Destroyer
    $.destroyModal = function (modal) {
        modal.modal('hide');
        modal.on('hidden.bs.modal', function () {
        });
    }

    // Card Progress Controller
    $.cardProgress = function (card, options) {
        var options = $.extend({
            dismiss: false,
            dismissText: 'Cancel',
            spinner: true,
            onDismiss: function () { }
        }, options);

        var me = $(card);

        me.addClass('card-progress');
        if (options.spinner == false) {
            me.addClass('remove-spinner');
        }

        if (options.dismiss == true) {
            var btn_dismiss = '<a class="btn btn-danger card-progress-dismiss">' + options.dismissText + '</a>';
            btn_dismiss = $(btn_dismiss).off('click').on('click', function () {
                me.removeClass('card-progress');
                me.find('.card-progress-dismiss').remove();
                options.onDismiss.call(this, me);
            });
            me.append(btn_dismiss);
        }

        return {
            dismiss: function (dismissed) {
                $.cardProgressDismiss(me, dismissed);
            }
        };
    }

    $.cardProgressDismiss = function (card, dismissed) {
        var me = $(card);
        me.removeClass('card-progress');
        me.find('.card-progress-dismiss').remove();
        if (dismissed)
            dismissed.call(this, me);
    }

    $.chatCtrl = function (element, chat, direction = 'top') {

        var chat = $.extend({
            position: 'chat-right',
            text: '',
            media_files: '',
            time: moment(new Date().toISOString()).format('hh:mm A'),
            chat_divider: '',
            chat_divider_date: '',
            picture: '',
            user_name: '',
            type: 'text', // or typing
            timeout: 0,
            onShow: function () { }
        }, chat);

        var vis = (!!chat.visiblity) ? 'visiblity_msg' : ''; // this class added for mark new msg as readed msg. its check msg is visible in chat and mark msg as readed msg.
        var media_files = '';
        var delete_btn = '';
        var chat_time_and_name = '';
        var chat_divider_html = '';

        if (chat.chat_divider == 'yes') {
            var date_to_delete = moment(chat.chat_divider_date).format('YYYYMMDD');
            // $(this).find("[data-divider='"+date_to_delete+"']").remove();
            var date_to_print = moment(chat.chat_divider_date).format('dddd, MMMM Do');
            chat_divider_html = '<div class="chat_divider" data-divider="' + date_to_delete + '">' + date_to_print + '</div>';
        }

        if (chat.position == 'chat-right') {

            if (chat.user_name !== '') {
                chat_time_and_name = chat.time + ' - ' + chat.user_name;
            } else {
                chat_time_and_name = chat.time;
            }

            delete_btn = '<a href="#" class="delete-msg dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></a><div class="dropdown-menu dropdown-menu-right"><a href="" data-msg_id="' + chat.msg_id + '" class="dropdown-item has-icon delete-msg-alert"><i class="far fa-trash-alt"></i> Delete Message</a></div>';

            if (!!chat.media_files) {
                $.each(chat.media_files, function (key, val) {
                    if (val['file_extension'] == 'image/jpg' || val['file_extension'] == 'image/png' || val['file_extension'] == 'image/jpeg') {
                        media_files += '<span class="chat-image-view"><a href="' + base_url + 'uploads/chat_media/' + val['file_name'] + '" download="' + val['original_file_name'] + '" class="download-btn-styling delete-msg fas fa-download"></a><img width="150px" src="' + base_url + 'uploads/chat_media/' + val['original_file_name'] + '" draggable="false"></span>';
                    } else {
                        media_files += '<span class="chat-files" style="position: relative;"><a href="' + base_url + 'assets/chats/' + val['file_name'] + '" download="' + val['original_file_name'] + '" class="download-btn-styling delete-msg fas fa-download"></a><div class="chat_media_img"><i class="fas fa-file-alt text-primary"></i></div><div class="chat_media_file">' + val['original_file_name'] + '</div><div class="chat_media_size">' + val['file_size'] + '</div></span>';
                    }
                });
            }

        } else {
            if (chat.user_name !== '') {
                chat_time_and_name = chat.user_name + ' - ' + chat.time;
            } else {
                chat_time_and_name = chat.time;
            }
            delete_btn = '';
            if (!!chat.media_files) {
                $.each(chat.media_files, function (key, val) {
                    if (val['file_extension'] == 'image/jpg' || val['file_extension'] == 'image/png' || val['file_extension'] == 'image/jpeg') {
                        media_files += '<span class="chat-image-view"><a href="' + base_url + 'assets/chats/' + val['original_file_name'] + '" download="' + val['original_file_name'] + '" class="download-btn-styling download-msg fas fa-download"></a><img width="150px" src="' + base_url + 'uploads/chat_media/' + val['original_file_name'] + '" draggable="false"></span>';
                    } else {
                        media_files += '<span class="chat-files" style="position: relative;"><a href="' + base_url + 'assets/chats/' + val['original_file_name'] + '" download="' + val['original_file_name'] + '" class="download-btn-styling download-msg fas fa-download"></a><div class="chat_media_img"><i class="fas fa-file-alt text-primary"></i></div><div class="chat_media_file">' + val['original_file_name'] + '</div><div class="chat_media_size">' + val['file_size'] + '</div></span>';
                    }
                });
            }
        }
        // var person_group_chat = '<span class="person-group-chat">'+chat.text+'</span>';
        var person_group_chat = '';
        var target = $(element),
            element = chat_divider_html + '<div class="chat-item ' + vis + ' ' + chat.position + '" data-delete_msg_id="' + chat.msg_id + '">' +
                '<div class="chat-avtar">' + chat.picture + '</div>' +
                '<div class="chat-details" style="position: relative;">' + delete_btn +
                '<div class="chat-text" ><span class="msg_text_media">' + person_group_chat + ' ' + chat.text + ' ' + media_files + '</span></div>' +
                '<div class="chat-time">' + chat_time_and_name + '</div>' +
                '</div>' +
                '</div>',
            typing_element = '';

        var append_element = element;

        if (chat.timeout > 0) {
            setTimeout(function () {
                if (direction == 'bottom')
                    target.find('.chat-content').append($(append_element).fadeIn());
                else
                    target.find('.chat-content').prepend($(append_element).fadeIn());
            }, chat.timeout);
        } else {

            if (direction == 'bottom')
                target.find('.chat-content').append($(append_element).fadeIn());
            else
                target.find('.chat-content').prepend($(append_element).fadeIn());
        }

        chat.onShow.call(this, append_element);
    }
})(jQuery, this, 0);

