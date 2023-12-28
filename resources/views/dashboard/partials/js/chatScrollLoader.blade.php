<script>
    $('#chat-pane-toggle').DirectChat('toggle')
    let page = 1;
    let loading = false;

    function viewChat(from, to) {
        page = 1;
        $('#direct-chat-message-list').empty();
        $('#chat-pane-toggle').DirectChat('toggle');
        $('#direct-chat-messages').attr('data-from', from)
        $('#direct-chat-messages').attr('data-to', to)
        loadChat(from, to)
    }

    function loadChat(from, to, page = 1) {
        $.ajax({
            url: "/user/messages",
            data: {
                from, to, page
            },
            beforeSend: function (xhr) {
                $('.overlay').show();
                loading = true;
            },
            success: function (data) {
                let message = '';

                $('#message_count').text(data.messages.total)

                if (data.messages && data.messages.data.length) {
                    $.each(data.messages.data, function (key, val) {
                        let created_date = (new Date(val.created_at)).toLocaleString();
                        let image = '';

                        if (val?.file?.url) {
                            image = `<img width='15%' src="${val.file.url}"/>`
                        }
                        if (val.from.id == from) {
                            message = `
                                <div class="direct-chat-msg">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-timestamp float-right">${created_date}</span>
                                    </div>
                                    <div class="direct-chat-text">
                                        ${val.message ?? image}
                                    </div>
                                </div>`
                        } else {
                            message = `
                                <div class="direct-chat-msg right">
                                    <div class="direct-chat-infos clearfix">
                                        <span
                                            class="direct-chat-name float-right">${val.to.first_name} ${val.to.last_name} ${val.to.patronymic_name}</span>
                                        <span class="direct-chat-timestamp float-left">${created_date}</span>
                                    </div>
                                    <div class="direct-chat-text">
                                        ${val.message ?? val.from}
                                    </div>
                                </div>`
                        }

                        $('#direct-chat-message-list').append(message)
                    })

                    loading = false;
                } else {
                    loading = true;
                }
            }
        }).done(function (res) {
            $('.overlay').hide()
        });
    }

    $('#direct-chat-messages').scroll(function () {
        let $this = $(this);
        let $results = $('#direct-chat-message-list');

        if ($this.scrollTop() + $this.height() >= $results.height()) {
            if (!loading) {
                page++;
                loadChat($this.attr('data-from'), $this.attr('data-to'), page)
            }
        }
    })
</script>
