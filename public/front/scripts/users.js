var User = function(){

	var init = function(){
		validateform();
	}

	var validateform = function()
	{
		
		$("#editProfile").validate({
			ignore: "",
			rules: {
				first_name:{
					required : true,
				},
				last_name:{
					required : true,
				},
				email:{
					required : true,
					email: true,
				},
				mobile:{
					required : true,
				},
			},


			highlight: function(element) {
				$(element).closest('.form-group').addClass('has-error');
			},

			unhighlight: function(element) {
				$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
				$(element).closest('.form-group').find('.help-block').html('');
			},
			errorPlacement: function (error, element) {
				$(element).closest('.form-group').find('.help-block').html($(error).html());
			}

		});

		$('#editProfile .submit-form').click(function () {

			if ($('#editProfile').validate().form()) {
				$('#editProfile .submit-form').prop('disabled', true);
				$('#editProfile .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
				setTimeout(function () {
					$('#editProfile').submit();
				}, 1000);
			}
			return false;
		});


		$('#editProfile').submit(function(e){

			var form = $(this);
			e.preventDefault(); 
			var form_data = new FormData($(this)[0]);
			var method = "POST";
        	var url = form.attr('action');

			$.ajax({
				type : method,
				url : url,
				dataType: 'json',
                data: form_data,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
	             success: function (data){
	        	if (data.type == 'success') 
	        	{
                	$('#editProfile .submit-form').prop('disabled', false);
	        		$('#editProfile .submit-form').html(lang.send);

	        	    $('#alert-message').fadeIn(2000).delay(3000).fadeOut(2000);
                    var message = '<i class="fa fa-check" aria-hidden="true"></i> <span>' + data.message + '</span> ';
                    $('#alert-message').html(message);

	        	} else {
	        		$('#editProfile .submit-form').prop('disabled', false);
	        		$('#editProfile .submit-form').html(lang.send);
	        		if (typeof data.errors === 'object') {
	        			console.log(data.errors);
	        			associate_errors(data.errors);
	        		} 
	        	}


	        },
	        error: function (xhr, textStatus, errorThrown) {
	        	$('#editProfile .submit-form').prop('disabled', false);
	            $('#editProfile .submit-form').html(lang.send);
	        	App.ajax_error_message(xhr);
	        },
	    }); 
		});


	}

	var associate_errors = function(errors, form)
	{
		$('.help-block').html('');
		$.each(errors,function(index, value)
		{
			var element = 'input[name='+index+']';
			$(element).closest('.form-group').addClass('has-error');
			$(element).closest('.form-group').find('.help-block').html(value);


		}
		);
	}
	
	
	

	

	return{

		init:function(){
			init();
		},
		
	}


}();

$(document).ready(function() {
	User.init();
});





