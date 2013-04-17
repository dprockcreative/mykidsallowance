var issues = {
    tracker : {
        init : function() {
            window.$issues = { 
                container : $('#issues')
            };

            $.ajax({
                type: "GET",
                url: '/assets/js/plugins/jquery.ajaxsubmit.js',
                dataType: 'script',
                async: false, 
                cache: true, 
                success: function(response) {}
            });

            $.ajax({
                url: '/issues/index/',
                async: false, 
                success: function(response) {
	                $issues.container.html(response);
				}
            });

            $('#issuetracker_form').live('submit', function() {
                $(this).ajaxSubmit({
                    target: '#issues'
                });
                return false;
            });
        }
    },
    comment : {
        init : function() {
            window.$comment = { 
                icform     : $('.issuecomment-form'),
                uiform     : $('.update-issue'),
                uicform    : $('.update-issue-comment')
            };

            $.ajax({
                url: '/assets/js/plugins/jquery.json.min.js',
                dataType: 'script',
                async: false, 
                cache: true,
                success: function(response) {}
            });

            result = false;

            $comment.icform.submit(function(e) {
                id       = $(this).attr('id');
                issue_id = $(this).attr('rel');
                name     = $(this).find('input[name="name"]').val();
                comment  = $(this).find('textarea[name="comment"]').val();
                $.ajax({ 
                    type: 'POST', 
                    url: '/issues/addcomment/issue_id/'+issue_id+'/',
                    data: 'name='+escape(name)+'&comment='+escape(comment),
                    success: function(response) {
                        json = $.evalJSON(response);
                        result = json.result;
                        $('#cf_'+issue_id).html(json.form);
                        if( result !== "false" ) {
                            $('#'+id).find('legend').text('Adding Comment');
                            _loadComment(this, issue_id);
                        }
                    },
                    complete: function() {
                        $('#'+id).find('legend').text('Add Comment');
                    }
                });
                return false;
            });

            $comment.uiform.live('submit', function(e) {
                var id = $(this).attr('id');
                var a  = ( $(this).find('input[name="active"]').is(':checked') ) ? 1:0;
                var s  = $(this).find('select[name="status"]').val();
                $.ajax({ 
                    type: "POST", 
                    url: $(this).attr('action'),
                    data: 'status='+s+'&active='+a,
                    success: function(response) {
                        if(response == 'false') {
                            alert("Issue Update Failed");
                        } else {
                            location.href=location.href;
                        }
                    }
                });
                return false;
            });

            $comment.uicform.live('submit', function(e) {
                var id = $(this).attr('id');
                var a  = ( $(this).find('input[name="active"]').is(':checked') ) ? 1:0;
                $.ajax({ 
                    type: "POST", 
                    url: $(this).attr('action'),
                    data: 'active='+a,
                    success: function(response) {
                        if(response == 'false') {
                            alert("Comment Update Failed");
                        } else {
                            if(a === 1) {
                                $('#'+id).closest('dl.comments').removeClass('inactive');
                            } else {
                                $('#'+id).closest('dl.comments').addClass('inactive');
                            }
                        }
                    }
                });
                return false;
            });
        }
    }
};

_loadComment = function(ele, issue_id) {
	$.ajax({
		url: '/issues/getcomment/issue_id/'+issue_id+'/',
		async: false, 
		success: function(response) {
			tar = $('#comments_'+issue_id);
			obj = $(response).hide();
			$(tar).append(obj);
			$(obj).slideDown();
		}
	});
};