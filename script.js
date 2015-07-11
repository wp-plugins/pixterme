jQuery(document).ready(function($)
{
	$('#register-pixter-me').click( function()
		{
			var email_reg = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
			$('#registration-form').find('.err').hide();
			var OK = true;
			if (!$('#tnc').is(':checked'))
			{
				$('#tnc').parent().find('.err').show();
				OK = false;
			}
			if (!email_reg.test($('#email').val()))
			{
				$('#email').parent().find('.err').show();
				OK = false;
			}
/*			else if ($('#fullname').val() == '')
			{
				alert('Name could not be empty');
			}	*/
			if (OK)
			{
				var data = {
					action: 'register_pixter',
//					fullname: $('#fullname').val(),
					email: $('#email').val()
				};
/*				$.post(pixterAjax.ajaxurl, data, function(response)
				{
					alert('Got this from the server: ' + response);
				});*/
				$.ajax({
					type: 'POST',
					url: pixterAjax.ajaxurl,
					dataType: 'json',
					cache: false,
					data: data,
					success: function(data)
					{
						if (data.success)
						{
							message = (typeof data.message == 'undefined') ? 'Thank you for registrating with us.' : data.message;
							$('#register-pm').append('<div style="width:100%;height:100%;position:absolute;top:0;left:0;margin:0" class="wrap"><h2 style="padding:90px;color:#009;">'+message+'</h2></div>');
							setTimeout( function()
							{
								$('#register-pm').hide();
								$('#admin_page_class').show();
							}, 3000);
						}
						else
						{
							message = (typeof data.message == 'undefined') ? 'Unknown server error.' : data.message;
							alert('Error:\n' + message);
						}
					},
					error : function(XMLHttpRequest, textStatus, errorThrown)
					{
						alert(textStatus+'\n'+XMLHttpRequest.responseText);
					}
				});
			}
		});
});