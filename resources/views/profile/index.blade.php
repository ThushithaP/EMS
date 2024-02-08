@extends('home')

@section('section')
<style>
    .pro-image{
        width: 200px !important;
    }
</style>
<script>
    $(document).ready(function() {
        $('#frmEditProfile').formValidation({
            fields: {
                full_name: {
                    verbose: false,
                    validators: {
                        notEmpty: {
                            message: 'Full name is required.'
                        },
                        regexp: {
                            regexp: /^[^-0-9]+$/i,
                            message: "Full name shouldn't contain number and -"
                        }
                    }
                },
                first_name: {
                    verbose: false,
                    validators: {
                        notEmpty: {
                            message: 'First name is required.'
                        },
                        regexp: {
                            regexp: /^[^-0-9]+$/i,
                            message: "First name shouldn't contain number and -"
                        }
                    }
                },
                last_name: {
                    verbose: false,
                    validators: {
                        notEmpty: {
                            message: 'Last name is required.'
                        },
                        regexp: {
                            regexp: /^[^-0-9]+$/i,
                            message: "Last name shouldn't contain number and -"
                        }
                    }
                },
                init_name: {
                    verbose: false,
                    validators: {
                        notEmpty: {
                            message: 'Last name is required.'
                        },
                        regexp: {
                            regexp: /^[^-0-9]+$/i,
                            message: "Last name shouldn't contain number and -"
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'The contact email is required.'
                        },
                        regexp: {
                            regexp: /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,
                            message: 'The input is not a valid email address.',
                        }
                    }
                },	
                nic: {
                    verbose: false,
                    validators: {
                        notEmpty: {
                            message: 'NIC is required.'
                        },
                    }
                },	
                mobile: {
                    verbose: false,
                    validators: {
                        notEmpty: {
                            message: 'Mobile number is required.'
                        },
                        regexp: {
                            regexp: /^[0-9]*$/,
                            message: 'Not a valid phone number.',
                        },
                        stringLength: {
                            min: 10,
                            max: 15,
                            message: 'At least 10 characters required',
                        }
                    }
                },	
                phone: {
                    verbose: false,
                    validators: {
                        regexp: {
                            regexp: /^[0-9]*$/,
                            message: 'Not a valid phone number.',
                        },
                        stringLength: {
                            min: 10,
                            max: 15,
                            message: 'At least 10 characters required.',
                        }
                    }
                },				
                department: {
                    verbose: false,
                    validators: {
                        notEmpty: {
                            message: 'Department is required.'
                        },
                        callback: {
                            message: 'Department is required',
                            callback: function(value) {
                                return value !== '0';
                            }
                        }
                    }
                },	
                designation: {
                    verbose: false,
                    validators: {
                        notEmpty: {
                            message: 'Designation is required.'
                        },
                        callback: {
                            message: 'Designation is required',
                            callback: function(value) {
                                return value !== '0';
                            }
                        }
                    }
                },	

            }
        })
    });

    function addRecord() {	
        $(this).on('success.form.fv', function(e) {
            e.preventDefault();
            // var dataString = $("#frmEditProfile").serialize();
            var formData = new FormData($('#frmEditProfile')[0]);
            $.ajax({
                url:'{{route("profile.update")}}',
                method : 'POST',
                data : formData,
                contentType: false,
                processData: false,

                success : function(response) {
                    showMessage('success',response.message);
                    location.reload();
                },
                error : function(error) {
                    showMessage('error',error.responseJSON.message);
                } 
            });     
        });		
	}

</script>
<div class="page-body ps-2">
	<div class="row justify-content-between py-2">
		<div class="col-auto">
			<p class="m-0 page-title">Profile</p>
		</div>
		<div class="col-auto">
			<ol class="p-0 m-0 breadcrumb">
				<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item active" aria-current="page">Profile</li>
			</ol>
		</div>
	</div>
    <div class="">
        <form id="frmEditProfile" method="post" action="" class="row form-horizontal">
            @csrf
			<div class="col-md-12">
				<div class="form-group mb-2">
					<label class="mb-1 control-label">Full Name</label>
					<input class="form-control" name="full_name" value="{{ $profile->full_name}}" type="text">
				</div>
				<div class="row justify-content-between">
					<div class="col-md-6">
						<div class="form-group mb-2">
							<label class="mb-1 control-label">First Name</label>
							<input class="form-control" name="first_name" value="{{ $profile->first_name}}" type="text">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 control-label">Name With Initials</label>
							<input class="form-control" name="init_name" value="{{ $profile->init_name}}" type="text">
						</div>
						
						<div class="form-group mb-2">
							<label class="mb-1 control-label">Phone</label>
							<input class="form-control" name="phone" value="{{ $profile->phone}}" type="text">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 control-label">Address 01</label>
							<textarea  class="form-control" name="address_1" rows="2">{{ $profile->address_1}}</textarea>
						</div>			
						<div class="form-group mb-2">
							<label class="mb-1 control-label">Photo</label>
                            @if($profile->image)
                            <img class="form-control rounded img-thumbnail pro-image mb-1" src="{{ asset($profile->image)}}"  alt="">
                            @endif
							<input class="form-control" name="image" type="file" value="{{ $profile->image }}"/>
						</div>			
					</div>
					<div class="col-md-6">
						<div class="form-group mb-2">
							<label class="mb-1 control-label">Last Name</label>
							<input class="form-control" name="last_name" value="{{ $profile->last_name}}" type="text">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 control-label">NIC</label>
							<input class="form-control" name="nic" value="{{ $profile->nic}}" type="text">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 control-label">Email</label>
							<input class="form-control" name="email" value="{{ $profile->email}}" type="email">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 control-label">Mobile</label>
							<input class="form-control" name="mobile" value="{{ $profile->mobile}}" type="text">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 control-label">Address 02</label>
							<textarea  class="form-control" name="address_2" rows="2">{{ $profile->address_2}}</textarea>
						</div>	
						<div class="form-group mb-2">
							<label class="mb-1 control-label">District</label>
							<input  class="form-control" name="district" value="{{ $profile->district}}"/>
						</div>	
					</div>
					<div class="d-flex justify-content-end mt-3">
                        <div class="form-group mb-2">
                            <input type="hidden" name="enc_staffid">
                            <button type="button" class="btn btn-sm mr-1 t-close-btn" onclick="window.location.href='/dashboard'">Close</button>
                            <button type="submit" class="btn btn-sm t-save-btn" onclick="javascript:addRecord()">Save</button>
                        </div>
					</div>
				</div>		
			</div>
		</form>
    </div>
</div>
@endsection
