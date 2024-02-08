@extends('home')

@section('section')

<script>
    var dtTable;
    var addDialog;
    var addDialogHTML;
    var viewDialog;
    var viewDialogHTML;
    var editDialog;
    var editDialogHTML;
	var permissionDialog;
	var permissionDialogHTML;
	var selectedRow =[];
    $(document).ready(function(){
		$.fn.dataTable.ext.errMode = 'none';
		dtTable = $('#dttable').dataTable({
			"aoColumns" : [
				{"data": "encid","bSortable" : false},
				{"data": "photo","bSortable" : false},
				{"data": "name","bSortable" : true},
				{"data": "email","bSortable" : false},
				{"data": "mobile","bSortable" : false},
				{"data": "designation","bSortable" : false},
				{"data": "emp_no","bSortable" : true},
				{"data": "status","bSortable" : false},
				{"data": "lmd","bSortable" : true},
				{"data": "action", "bSortable" : false, "render": function (data, type, row) {
					return data; // This will render the custom HTML provided in the 'action' field
				}},
			],
			"aaSorting": [[8, 'desc']],
			"processing": true,
			"fnDrawCallback" : function() {
				$('.js-init-tooltip').tooltip();
				$('.js-row-select').change(function() {
					var rowID = $(this).val();
					if(this.checked && !selectedRow.includes(rowID)) {
						selectedRow.push(rowID);
					} else {
						selectedRow = selectedRow.filter(function(row){
							return row !== rowID;
						});
					}
					enableDisableButton();	
				});	
			},
			"pageLength": "{{DEFAULT_TABLE_ROW}}",
			// "lengthMenu": [[10,25, 50, 100, -1], [10,25, 50, 100, 'All']],
			"responsive" : true,
			"bAutoWidth": false,
			"searching": true,
			"bServerSide" : true,
			"ajax" : {
				"url" : "{{ route('staff.list') }}",
				"data" : function(d) {
					d.status = window.localStorage.status ? window.localStorage.status : $('#status').val();
					d.staff_designation = window.localStorage.staff_designation ? window.localStorage.staff_designation : $('#staff_designation').val();
				},			
			}
		});
        addDialogHTML = $(".js-add-dialog").html();
		$(".js-add-dialog").html('');
        viewDialogHTML = $(".js-view-dialog").html();
		$(".js-view-dialog").html('');
        editDialogHTML = $(".js-edit-dialog").html();
		$(".js-edit-dialog").html('');
        permissionDialogHTML = $(".js-permission-dialog").html();
		$(".js-permission-dialog").html('');

		$(".js-row-select-all").change(function() {
			selectedRow.length = 0;
			if (this.checked) {
				$('.js-row-select').prop('checked', true).trigger('change');
			} else {
				$('.js-row-select').prop('checked', false).trigger('change');
			}
		});

		var searchInput = $('#dttable_filter').find('input[type="search"]').detach();
		var searchIcon = '<i class="bi bi-search search-icon"></i>'
    	$('.search-bar').append(searchInput);
		searchInput.addClass('search-box form-control');
		searchInput.attr('placeholder','Search');
    	$('.search-box').after(searchIcon);
		
		$('#dttable_filter').detach();
		var length = $('#dttable_length').detach();
		length.addClass('float-left');
		$('.page-length').append(length);

		$("#status").change(function() {
			window.localStorage.status = ''; 
			refreshDataTable(true);
		});
		$("#staff_designation").change(function() {
			window.localStorage.staff_designation = ''; 
			refreshDataTable(true);
		});
    });

	function refreshDataTable() {
		dtTable.fnDraw(true);
		selectedRow.length = 0;
		$('.js-row-select').prop('checked', false);
		$('.js-row-select-all').prop('checked', false);
		enableDisableButton();
	}

	function enableDisableButton() {
		var btns = $('.bulk-btn-func');
		if(selectedRow.length > 0) {
			btns.attr('disabled',false)
		} else {
			btns.attr('disabled',true)
		}
	}

	function closeAddRecord() {
		addDialog.modal('hide');
	}

    function addRecord() {
		addDialog = bootbox.dialog({
			message: addDialogHTML,
			title: "Add Staff Member",
			className:"modal-lg t-modal"
		})
		.on("shown.bs.modal", function(e) {
			$('#frmAddStaff').formValidation({
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
			}).on('success.form.fv', function(e) {
				e.preventDefault();
				var dataString = $("#frmAddStaff").serialize();
				// var formData = new FormData($('#frmAddStaff')[0]);
                $.ajax({
                    url:'{{route("staff.store")}}',
                    method : 'POST',
                    data : dataString,
					// contentType: false,
                	// processData: false,

                    success : function(response) {
						showMessage('success',response.message);
						closeAddRecord();
						refreshDataTable();
                    },
                    error : function(error) {
						showMessage('error',error.responseJSON.message);
                    } 
                })
			
			});
		});
	}

	function closeViewRecord() {
		viewDialog.modal('hide');
	}

    function viewRecord(id) {
		viewDialog = bootbox.dialog({
			message: viewDialogHTML,
			title: "View Staff",
			className:"modal-lg t-modal"
		})
		.on("shown.bs.modal", function(e) {
			$.ajax({
				url:'{{route("staff.show")}}',
				method : 'POST',
				data : {
					id: id,
					_token: '{{ csrf_token() }}' 
				},

				success : function(response) {
					var staff = response.user;
					var imageUrl = '{{ env("BASE_URL") }}'+ staff.image;
					$('.js-staff-name').text(staff.full_name); 
					$('.js-staff-fname').text(staff.first_name);
					$('.js-staff-iname').text(staff.init_name);
					$('.js-staff-designation').text(staff.desig_name);
					$('.js-staff-phone').text(staff.phone); 
					$('.js-staff-emp').text(staff.emp_no);
					$('.js-staff-add1').text(staff.address_1);
					$('.js-staff-lname').text(staff.last_name);
					$('.js-staff-nic').text(staff.nic); 
					$('.js-staff-email').text(staff.email);
					$('.js-staff-mobile').text(staff.mobile);
					$('.js-staff-dep').text(staff.dep_name);
					$('.js-staff-add2').text(staff.address_2);
					$('.js-staff-district').text(staff.district);
					$('.js-staff-photo').attr('src', imageUrl);				
				},
				error : function(error) {
					closeViewRecord();
					showMessage('error',error.responseJSON.message);
				} 
			});
		});
	}

    function exportToExcel() {
		$.ajax({
			url: '{{ route("staff.export") }}',
			method: 'POST',
			data: {
				id : selectedRow,
				_token: '{{ csrf_token() }}' 
			},
			xhrFields: {
				responseType: 'blob' 
			},
			success: function(response) {
				var url = window.URL.createObjectURL(response);
				var a = document.createElement('a');
				a.href = url;
				var now = new Date().toLocaleString();
				a.download = 'staff_'+now+'.xlsx'; 
				document.body.appendChild(a);
				a.click();
				window.URL.revokeObjectURL(url);
				$(a).remove();
			},
			error: function(error) {
				showMessage('error', error);
			} 
		});	
	}

	function closeEditRecord() {
		editDialog.modal('hide');
	}

	function editRecord(id) {
		editDialog = bootbox.dialog({
			message: editDialogHTML,
			title: "Edit Staff Member",
			className:"modal-lg t-modal"
		})
		.on("shown.bs.modal", function(e) {
			$('#frmEditStaff').formValidation({
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
			}).on('success.form.fv', function(e) {
				e.preventDefault();
				var dataString = $("#frmEditStaff").serialize();
				var formData = new FormData($('#frmEditStaff')[0]);
                $.ajax({
                    url:'{{route("staff.update")}}',
                    method : 'POST',
                    data : dataString,
                    success : function(response) {
						showMessage('success',response.message);
						closeEditRecord();
						refreshDataTable();
                    },
                    error : function(error) {
						showMessage('error',error.responseJSON.message);
                    } 
                });	
			});
			$.ajax({
				url:'{{route("staff.show")}}',
				method : 'POST',
				data : {
					id: id,
					_token: '{{ csrf_token() }}' 
				},
				success : function(response) {
					var staff = response.user;
					$('[name="full_name"]').val(staff.full_name);
					$('[name="first_name"]').val(staff.first_name);
					$('[name="last_name"]').val(staff.last_name);
					$('[name="init_name"]').val(staff.init_name);
					$('[name="email"]').val(staff.email);
					$('[name="nic"]').val(staff.nic);
					$('[name="designation"]').val(staff.designation);
					$('[name="mobile"]').val(staff.mobile);
					$('[name="phone"]').val(staff.phone);
					$('[name="department"]').val(staff.department);
					$('[name="emp_no"]').val(staff.emp_no);
					$('[name="address_1"]').val(staff.address_1);
					$('[name="address_2"]').val(staff.address_2);
					$('[name="district"]').val(staff.district);
					$('[name="enc_staffid"]').val(staff.encId);					
				},
				error : function(error) {
					closeEditRecord();
					showMessage('error',error.responseJSON.message);
				} 
			});
		});
	}

	function resignedMember(id) {
		confirmCustomPopup("Resigned Staff Member", "Are you sure to resigned this staff member?", 'Y/N', function() {
			sendAjaxRequest('{{route("staff.status")}}', {
				'id': id,
				'act': 'resigned',
				'_token': '{{ csrf_token() }}'
			});
		});
	}

	function availableMember(id) {
		confirmCustomPopup("Available Staff Member", "Are you sure to available this staff member?", 'Y/N', function() {
			sendAjaxRequest('{{route("staff.status")}}', {
				'id': id,
				'act': 'available',
				'_token': '{{ csrf_token() }}'
			});
		});
	}

	function terminateMember(id) {
		confirmCustomPopup("Terminated Staff Member", "Are you sure to terminate this staff member?", 'Y/N', function() {
			sendAjaxRequest('{{route("staff.status")}}', {
				'id': id,
				'act': 'terminated',
				'_token': '{{ csrf_token() }}'
			});
		});
	}

	function suspendedMember(id) {
		confirmCustomPopup("Suspended Staff Member", "Are you sure to suspend this staff member?", 'Y/N', function() {
			sendAjaxRequest('{{route("staff.status")}}', {
				'id': id,
				'act': 'suspended',
				'_token': '{{ csrf_token() }}'
			});
		});
	}

	function deleteRecord(id) {
		confirmCustomPopup("Delete Staff Member", "Are you sure to delete this staff member?", 'Y/N', function() {
			sendAjaxRequest('{{route("staff.status")}}', {
				'id': id,
				'act': 'delete',
				'_token': '{{ csrf_token() }}'
			});
		});
	}

	function terminateMemberBulk() {
		confirmCustomPopup("Terminated Staff Member(s)", "Are you sure to terminate selected "+ selectedRow.length+" staff member(s)?", 'Y/N', function() {
			sendAjaxRequest('{{route("staff.status")}}', {
				'id': selectedRow,
				'act': 'terminated',
				'_token': '{{ csrf_token() }}'
			});
		});
	}

	function suspendedMemberBulk(id) {
		confirmCustomPopup("Suspended Staff Member(s)", "Are you sure to suspend selected "+ selectedRow.length+" staff member?", 'Y/N', function() {
			sendAjaxRequest('{{route("staff.status")}}', {
				'id': selectedRow,
				'act': 'suspended',
				'_token': '{{ csrf_token() }}'
			});
		});
	}
	
	function deleteRecordBulk(id) {
		confirmCustomPopup("Delete Staff Member(s)", "Are you sure to delete selected "+ selectedRow.length+" staff member(s)?", 'Y/N', function() {
			sendAjaxRequest('{{route("staff.status")}}', {
				'id': selectedRow,
				'act': 'delete',
				'_token': '{{ csrf_token() }}'
			});
		});
	}

	function closeChangePermission(){
		permissionDialog.modal('hide');
	}

	function changePermission(id) {
		permissionDialog = bootbox.dialog({
			message: permissionDialogHTML,
			title : "Permission Granted",
			className : "t-modal"
		})	
		.on("shown.bs.modal", function(e) {
			$('#frmPermissionStaff').formValidation({
			})
			.on('success.form.fv', function(e) {
				e.preventDefault();
				$('[name="enc_userId"]').val(id);
				var dataString = $("#frmPermissionStaff").serialize();
                $.ajax({
                    url:'{{route("staff.setpermission")}}',
                    method : 'POST',
                    data : dataString,
                    success : function(response) {
						showMessage('success',response.message);
						closeChangePermission();
						refreshDataTable();
                    },
                    error : function(error) {
						showMessage('error',error.responseJSON.message);
                    } 
                });	
			});
			$.ajax({
				url:'{{route("staff.permission")}}',
				method : 'POST',
				data : {
					id: id,
					_token: '{{ csrf_token() }}' 
				},
				success : function(response) {
					var permissions = response.permissions;
					var setActions = response.setActions;
					console.log(setActions);
					var html = '';
					html = '<table style="border-collapse: unset !important;"><tbody>';

					$.each(permissions,function(sectionId,section) {
						html += '<tr>\
									<th>' + section.section_name + '</th>\
								</tr>';
						
								$.each(section.actions , function(index,action) {
									var isChecked = setActions.some(function(setAction) {
										return setAction.action_id === action.action_id;
									});
									var checkedAttribute = isChecked ? 'checked' : '';
									html += '<tr class="col-12">\
												<td class="col-1"><input type="checkbox" name="actions[]" class="actionCheckbox" value="' + action.action_id + '" ' + checkedAttribute + '></td>\
												<td>' + action.action_name + '</td>\
											</tr>';
								})
					});
					html += '</tbody></table>';				
					$('.js-html').before(html);
					
				},
				error : function(error) {
					closeEditRecord();
					showMessage('error',error.responseJSON.message);
				} 
			});
		});
	}

	function resetPassword(id) {
		confirmCustomPopup("Reset Password", "Are you sure to reset password this staff member?", 'Y/N', function() {
			sendAjaxRequest('{{route("staff.reset")}}', {
				'id': id,
				'_token': '{{ csrf_token() }}'
			});
		});
	}

</script>

<div class="page-body ps-2">
	<div class="row justify-content-between py-2">
		<div class="col-auto">
			<p class="m-0 page-title">Staff</p>
		</div>
		<div class="col-auto">
			<ol class="p-0 m-0 breadcrumb">
				<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item active" aria-current="page">Staff</li>
			</ol>
		</div>
	</div>
	<div class="row justify-content-between py-2 mb-2 page-btn-line">
		<div class="col-auto">
			@if($addGranted)
				<button onclick="addRecord();" type="button" class="ml-2 btn btn-sm js-init-tooltip" data-inline="true" title="Add New Staff Member">
					<i class="bi bi-plus-circle add-btn"></i>
				</button>
			@endif
		</div>
    <div class="col-8">
        <div class="search-bar d-flex"></div>
    </div>
    <div class="col-auto">
        <div class="js-bulk-btn">          
            @if($exportGranted)
                <button onclick="exportToExcel();" type="button" class="ml-2 btn btn-sm me-4 bulk-btn-func js-init-tooltip" disabled="true" data-inline="true" title="Export To Excel">
                    <i class="bi bi-filetype-xlsx xlsx-btn"></i>
                </button>
            @endif
            @if($statusChangeGranted)
                <button onclick="terminateMemberBulk()" type="button" class="ml-2 btn btn-sm bulk-btn-func js-init-tooltip" disabled="true" data-inline="true" title="Terminate Staff">
					<i class="bi bi-x-circle bulk-btn"></i>
                </button>
                <button onclick="suspendedMemberBulk()" type="button" class="ml-2 btn btn-sm bulk-btn-func js-init-tooltip" disabled="true" data-inline="true" title="Suspend Staff">
					<i class="bi bi-pause-circle bulk-btn"></i>
                </button>
            @endif
            @if($deleteGranted)
                <button onclick="deleteRecordBulk();" type="button" class="ml-2 btn btn-sm bulk-btn-func js-init-tooltip" disabled="true" data-inline="true" title="Delete">
                    <i class="bi bi-trash bulk-btn"></i>
                </button>
            @endif
        </div>
    </div>
	<div class="col-12 d-flex mb-2 mt-3">
		<div class="col-2 page-length"></div>
		<div class="col-9 d-flex">
			<div class="me-2">
				<label for="status">Status</label>
				<select name="status" id="status">
					<option value="">All</option>
					@foreach($status as $key => $value)
						<option value="{{ $key }}">{{ $value }}</option>
					@endforeach
				</select>
			</div>
			<div class="me-2">
				<label for="staff_designation">Designation</label>
				<select name="staff_designation" id="staff_designation">
					<option value="">All</option>
					@foreach($designations as $key => $value)
						<option value="{{ $key }}">{{ $value }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="col-1">
			<a class="float-end" href="javascript:refreshDataTable()"><i class="bi bi-arrow-clockwise"></i></a>
		</div>
	</div>
	
	<table id="dttable"	class="table table-striped table-hover" style="width:100%" >
	<thead>
		<tr>
		<th><input type="checkbox" class="js-row-select-all"></th>
		<th>Photo</th>
		<th>Name</th>
		<th>Email</th>
		<th>Mobile</th>
		<th>Designation</th>
		<th>Emp No</th>
		<th>Status</th>
		<th>Last Modified Date</th>
		<th>Action</th>
		</tr>
	</thead>
	<tbody>
					
	</tbody>
	</table>
</div>
<div class="d-none">
    <div class="js-add-dialog">
		<form id="frmAddStaff" method="post" action="" class="row form-horizontal">
            @csrf
			<div class="col-md-12">
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Full Name</label>
					<input class="form-control form-control-sm" name="full_name" type="text" autocomplete="off" autocomplete="off">
				</div>
				<div class="row justify-content-between">
					<div class="col-md-6">
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">First Name</label>
							<input class="form-control form-control-sm" name="first_name" type="text" autocomplete="off" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Name With Initials</label>
							<input class="form-control form-control-sm" name="init_name" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Designation</label>
							<select class="form-control form-control-sm" name="designation">
								<option value="">Please Select</option>
								@foreach($designations as $key => $value)
       								<option value="{{ $key }}">{{ $value }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Phone</label>
							<input class="form-control form-control-sm" name="phone" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Employee Number</label>
							<input class="form-control form-control-sm" name="emp_no" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Address 01</label>
							<textarea  class="form-control form-control-sm" name="address_1" rows="2" autocomplete="off"></textarea>
						</div>			
						<!-- <div class="form-group mb-2">
							<label class="mb-1 t-main-label">Photo</label>
							<input class="form-control form-control-sm" name="image" type="file"/>
						</div>			 -->
					</div>
					<div class="col-md-6">
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Last Name</label>
							<input class="form-control form-control-sm" name="last_name" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">NIC</label>
							<input class="form-control form-control-sm" name="nic" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Email</label>
							<input class="form-control form-control-sm" name="email" type="email" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Mobile</label>
							<input class="form-control form-control-sm" name="mobile" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Department</label>
							<select class="form-control form-control-sm" name="department">
								<option value="">Please Select</option>
								@foreach($departments as $department)
       								<option value="{{ $department['id'] }}">{{ $department['name'] }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Address 02</label>
							<textarea  class="form-control form-control-sm" name="address_2" rows="2" autocomplete="off"></textarea>
						</div>	
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">District</label>
							<input  class="form-control form-control-sm" name="district" type="text" autocomplete="off"/>
						</div>	
					</div>
						
					<div class="mt-3 d-flex justify-content-end">
						<div class="form-group mb-2 foot mb-0">
							<button type="button" class="btn btn-sm mr-1 t-close-btn" onclick="javascript:closeAddRecord()">Close</button>
							<button type="submit" class="btn btn-sm t-save-btn">Save</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>

    <div class="js-view-dialog">
		<div class="row form-horizontal">
			<div class="col-md-12">
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Full Name</label>
					<div class="js-staff-name"></div>
				</div>
				<div class="row justify-content-between">
					<div class="col-md-6">
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">First Name</label>
							<div class="js-staff-fname t-sub-label" ></div>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Name With Initials</label>
							<div class="js-staff-iname t-sub-label" ></div>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Designation</label>
							<div class="js-staff-designation t-sub-label" ></div>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Phone</label>
							<div class="js-staff-phone t-sub-label" ></div>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Employee Number</label>
							<div class="js-staff-emp t-sub-label" ></div>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Address 01</label>
							<div class="js-staff-add1 t-sub-label" ></div>
						</div>			
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Photo</label>
							<img width="50px" class="js-staff-photo" src="" alt="">
						</div>			
					</div>
					<div class="col-md-6">
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Last Name</label>
							<div class="js-staff-lname t-sub-label" ></div>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">NIC</label>
							<div class="js-staff-nic t-sub-label"></div>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Email</label>
							<div class="js-staff-email t-sub-label"></div>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Mobile</label>
							<div class="js-staff-mobile t-sub-label"></div>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Department</label>
							<div class="js-staff-dep t-sub-label"></div>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Address 02</label>
							<div class="js-staff-add2 t-sub-label"></div>
						</div>	
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">District</label>
							<div class="js-staff-district t-sub-label"></div>
						</div>	
					</div>
				</div>
				<div class="row justify-content-end mt-3">
					<div class="col-auto">
						<div class="form-group foot mb-0">
							<button type="button" class="btn btn-sm mr-1 t-close-btn" onclick="javascript:closeViewRecord()">Close</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="js-edit-dialog">
		<form id="frmEditStaff" method="post" action="" class="row form-horizontal">
            @csrf
			<div class="col-md-12">
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Full Name</label>
					<input class="form-control form-control-sm" name="full_name" type="text" autocomplete="off">
				</div>
				<div class="row justify-content-between">
					<div class="col-md-6">
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">First Name</label>
							<input class="form-control form-control-sm" name="first_name" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Name With Initials</label>
							<input class="form-control form-control-sm" name="init_name" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Designation</label>
							<select class="form-control form-control-sm" name="designation">
								<option value="">Please Select</option>
								@foreach($designations as $key => $value)
       								<option value="{{ $key }}">{{ $value }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Phone</label>
							<input class="form-control form-control-sm" name="phone" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Employee Number</label>
							<input class="form-control form-control-sm" name="emp_no" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Address 01</label>
							<textarea  class="form-control form-control-sm" name="address_1" rows="2" autocomplete="off"></textarea>
						</div>			
						<!-- <div class="form-group mb-2">
							<label class="mb-1 t-main-label">Photo</label>
							<input class="form-control form-control-sm" name="image" type="file"/>
						</div>			 -->
					</div>
					<div class="col-md-6">
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Last Name</label>
							<input class="form-control form-control-sm" name="last_name" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">NIC</label>
							<input class="form-control form-control-sm" name="nic" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Email</label>
							<input class="form-control form-control-sm" name="email" type="email" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Mobile</label>
							<input class="form-control form-control-sm" name="mobile" type="text" autocomplete="off">
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Department</label>
							<select class="form-control form-control-sm" name="department">
								<option value="">Please Select</option>
								@foreach($departments as $department)
       								<option value="{{ $department['id'] }}">{{ $department['name'] }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">Address 02</label>
							<textarea  class="form-control form-control-sm" name="address_2" rows="2" autocomplete="off"></textarea>
						</div>	
						<div class="form-group mb-2">
							<label class="mb-1 t-main-label">District</label>
							<input  class="form-control form-control-sm" name="district" type="text" autocomplete="off"/>
						</div>	
					</div>
					<div class="mt-3 d-flex justify-content-end">
						<div class="form-group mb-2 foot mb-0">
							<input type="hidden" name="enc_staffid">
							<button type="button" class="btn btn-sm mr-1 t-close-btn" onclick="javascript:closeEditRecord()">Close</button>
							<button type="submit" class="btn btn-sm t-save-btn">Save</button>
						</div>
					</div>
				</div>		
			</div>
		</form>
	</div>

	<div class="js-permission-dialog">
		<form id="frmPermissionStaff" method="post" action="" class="row form-horizontal">
            @csrf
			<div class="col-md-12">
				<div class="row justify-content-between">
					<div class="row justify-content-end">
						<div class="col-auto js-html">
							<div class="form-group mb-2 foot mb-0">
								<input type="hidden" name="enc_userId">
								<button type="button" class="btn btn-sm mr-1 t-close-btn" onclick="javascript:closeChangePermission()">Close</button>
								<button type="submit" class="btn btn-sm t-save-btn">Save</button>
							</div>
						</div>
					</div>
				</div>		
			</div>
		</form>
	</div>
</div>   
@endsection