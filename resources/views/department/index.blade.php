@extends('home')

@section('section')

@php
	use App\Models\Permission;
@endphp
<script>
    var dtTable;
    var addDialog;
    var addDialogHTML;
    var viewDialog;
    var viewDialogHTML;
    var editDialog;
    var editDialogHTML;
	var selectedRow =[];
    $(document).ready(function(){
		$.fn.dataTable.ext.errMode = 'none';
		dtTable = $('#dttable').dataTable({
			"aoColumns" : [
				{"data": "encid","bSortable" : false},
				{"data": "name","bSortable" : true},
				{"data": "email","bSortable" : false},
				{"data": "status","bSortable" : false},
				{"data": "lmd","bSortable" : true},
				{"data": "action", "bSortable" : false, "render": function (data, type, row) {
					return data; // This will render the custom HTML provided in the 'action' field
				}},
			],
			"aaSorting": [[4, 'desc']],
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
			"searching": true,
			"bServerSide" : true,
			"ajax" : {
				"url" : "{{ route('department.list') }}",
				"data" : function(d) {
					d.status = window.localStorage.status ? window.localStorage.status : $('#status').val();
				},
				
			}
		});
        addDialogHTML = $(".js-add-dialog").html();
		$(".js-add-dialog").html('');
        viewDialogHTML = $(".js-view-dialog").html();
		$(".js-view-dialog").html('');
        editDialogHTML = $(".js-edit-dialog").html();
		$(".js-edit-dialog").html('');

		$(".js-row-select-all").change(function() {
			selectedRow.length = 0;
			if (this.checked){
				$('.js-row-select').prop('checked', true).trigger('change');
			}
			else{
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
			window.localStorage.status = ''; //$("#status").val();
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
			title: "Add Department",
			className:"t-modal"
		})
		.on("shown.bs.modal", function(e) {
			$('#frmAddDepartment').formValidation({
				fields: {
					dep_name: {
						verbose: false,
						validators: {
							notEmpty: {
								message: 'The department name is required.'
							},
							regexp: {
								regexp: /^[^-0-9]+$/i,
								message: "Department name shouldn't contain number and -"
							}
						}
					},
                    dep_email: {
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
				}
			}).on('success.form.fv', function(e) {
				e.preventDefault();
				var dataString = $("#frmAddDepartment").serialize();
                $.ajax({
                    url:'{{route("department.store")}}',
                    method : 'POST',
                    data : dataString,

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
			title: "View Department",
			className:"t-modal"
		})
		.on("shown.bs.modal", function(e) {
			$.ajax({
				url:'{{route("department.show")}}',
				method : 'POST',
				data : {
					id: id,
					_token: '{{ csrf_token() }}' 
				},

				success : function(response) {
					var dep = response.department;
					var status = (response.dep_status=='A') ? 'Active' : 'Inactive';

					$('.js-dep-name').text(dep.dep_name); 
					$('.js-dep-email').text(dep.dep_email);
					$('.js-dep-desc').text(dep.description);
					$('.js-dep-status').text(status);
					
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
			url: '{{ route("department.export") }}',
			method: 'POST',
			data: {
				id : selectedRow,
				search : $('.search-box').val(),
				status : $('#status').val(),
				_token: '{{ csrf_token() }}' 
			},
			xhrFields: {
				responseType: 'blob' // Set the response type to blob
			},
			success: function(response) {
				var url = window.URL.createObjectURL(response);
				var a = document.createElement('a');
				a.href = url;
				var now = new Date().toLocaleString();
				a.download = 'departments_'+now+'.xlsx'; 
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
			title: "Edit Department",
			className:"t-modal"
		})
		.on("shown.bs.modal", function(e) {
			$('#frmEditDepartment').formValidation({
				fields: {
					dep_name: {
						verbose: false,
						validators: {
							notEmpty: {
								message: 'The department name is required.'
							},
							regexp: {
								regexp: /^[^-0-9]+$/i,
								message: "Department name shouldn't contain number and -"
							}
						}
					},
                    dep_email: {
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
				}
			}).on('success.form.fv', function(e) {
				e.preventDefault();
				var dataString = $("#frmEditDepartment").serialize();
                $.ajax({
                    url:'{{route("department.update")}}',
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
                })
			
			});
			$.ajax({
				url:'{{route("department.show")}}',
				method : 'POST',
				data : {
					id: id,
					_token: '{{ csrf_token() }}' 
				},

				success : function(response) {
					var dep = response.department;
					var status = (response.dep_status=='A') ? 'Active' : 'Inactive';
					console.log(response);
					$('[name="dep_name"]').val(dep.dep_name); 
					$('[name="dep_email"]').val(dep.dep_email);
					$('[name="dep_desc"]').val(dep.description);
					$('[name="dep_status"]').val(dep.dep_status);
					$('[name="enc_depid"]').val(id);
					
				},
				error : function(error) {
					closeEditRecord();
					showMessage('error',error.responseJSON.message);
				} 
			});
		});
	}

	function inoperativeRecord(id) {
		confirmCustomPopup("Inoperative", "Are you sure to inoperative this department?", 'Y/N', function() {
			sendAjaxRequest('{{route("department.status")}}', {
				'id': id,
				'act': 'inoperative',
				'_token': '{{ csrf_token() }}'
			});
		});
	}

	function operativeRecord(id) {
		confirmCustomPopup("Operative", "Are you sure to operative this department?", 'Y/N', function() {
			sendAjaxRequest('{{route("department.status")}}', {
				'id': id,
				'act': 'operative',
				'_token': '{{ csrf_token() }}'
			});
		});
	}

	function deleteRecord(id) {
		confirmCustomPopup("Delete", "Are you sure to delete this department?", 'Y/N', function() {
			sendAjaxRequest('{{route("department.status")}}', {
				'id': id,
				'act': 'delete',
				'_token': '{{ csrf_token() }}'
			});
		});
	}

	function inoperativeRecordBulk() {
		confirmCustomPopup("Inoperative", "Are you sure to inoperative selected "+ selectedRow.length+" department(s)?", 'Y/N', function() {
			sendAjaxRequest('{{route("department.status")}}', {
				'id': selectedRow,
				'act': 'inoperative',
				'_token': '{{ csrf_token() }}'
			});
		});
	}

	function operativeRecordBulk(id) {
		confirmCustomPopup("Operative", "Are you sure to operative selected "+ selectedRow.length+" department?", 'Y/N', function() {
			sendAjaxRequest('{{route("department.status")}}', {
				'id': selectedRow,
				'act': 'operative',
				'_token': '{{ csrf_token() }}'
			});
		});
	}
	
	function deleteRecordBulk(id) {
		confirmCustomPopup("Delete", "Are you sure to delete selected "+ selectedRow.length+" department?", 'Y/N', function() {
			sendAjaxRequest('{{route("department.status")}}', {
				'id': selectedRow,
				'act': 'delete',
				'_token': '{{ csrf_token() }}'
			});
		});
	}
</script>

<div class="page-body ps-2">
	<div class="row justify-content-between py-2">
		<div class="col-auto">
			<p class="m-0 page-title">Department</p>
		</div>
		<div class="col-auto">
			<ol class="p-0 m-0 breadcrumb">
				<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item active" aria-current="page">Department</li>
			</ol>
		</div>
	</div>
	<div class="row justify-content-between py-2 mb-2 page-btn-line">
		<div class="col-auto">
			@if(Permission::checkPermission(Permission::ADD_DEPARTMENT))
				<button onclick="addRecord();" type="button" class="ml-2 btn btn-sm" data-inline="true" title="Add New Department">
					<i class="bi bi-plus-circle add-btn"></i>
				</button>
			@endif
		</div>
    <div class="col-8">
        <div class="search-bar d-flex"></div>
    </div>
    <div class="col-auto">
        <div class="js-bulk-btn">          
            @if(Permission::checkPermission(Permission::EXPORT_DEPARTMENT_TO_EXCEL))
                <button onclick="exportToExcel();" type="button" class="ml-2 btn btn-sm me-4 bulk-btn-func js-init-tooltip" disabled="true" data-inline="true" title="Export To Excel">
                    <i class="bi bi-filetype-xlsx xlsx-btn"></i>
                </button>
            @endif
            @if(Permission::checkPermission(Permission::STATUS_CHANGE_DEPARTMENT))
                <button onclick="operativeRecordBulk();" type="button" class="ml-2 btn btn-sm bulk-btn-func js-init-tooltip" disabled="true" data-inline="true" title="Operative">
                    <i class="bi bi-check-circle active-btn"></i>
                </button>
                <button onclick="inoperativeRecordBulk();" type="button" class="ml-2 btn btn-sm bulk-btn-func js-init-tooltip" disabled="true" data-inline="true" title="Inoperative">
                    <i class="bi bi-exclamation-circle inactive-btn"></i>
                </button>
            @endif
            @if(Permission::checkPermission(Permission::DELETE_DEPARTMENT))
                <button onclick="deleteRecordBulk();" type="button" class="ml-2 btn btn-sm bulk-btn-func js-init-tooltip" disabled="true" data-inline="true" title="Delete">
                    <i class="bi bi-trash-fill delete-btn"></i>
                </button>
            @endif
        </div>
    </div>
	<div class="col-12 d-flex mb-2 mt-3">
		<div class="col-2 page-length"></div>
		<div class="col-9">
			<div class="">
				<label for="">Status</label>
				<select name="status" id="status">
					<option value="">All</option>
					<option value="O">Operative</option>
					<option value="I">Inperative</option>
				</select>
			</div>
		</div>
		<div class="col-1">
			<a class="float-end js-init-tooltip" href="javascript:refreshDataTable()"><i class="bi bi-arrow-clockwise"></i></a>
		</div>
	</div>

	<table id="dttable"	class="table table-striped table-hover t-table" style="width:100%" >
	<thead class="t-head">
		<tr>
		<th><input type="checkbox" class="js-row-select-all"></th>
		<th>Department Name</th>
		<th>Email</th>
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
		<form id="frmAddDepartment" method="post" action="" class="row form-horizontal">
            @csrf
			<div class="col-md-12">
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Department Name</label>
                    <input class="form-control form-control-sm" name="dep_name" type="text" autocomplete="off">
				</div>
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Email</label>
                    <input class="form-control form-control-sm" name="dep_email" type="email" autocomplete="off">
				</div>
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Description</label>
					<textarea  class="form-control form-control-sm" name="dep_desc" rows="2" autocomplete="off"></textarea>
				</div>			
				<div class="row justify-content-end mt-3">
					<div class="col-auto">
						<div class="form-group foot mb-0">
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
					<label class="mb-1 t-main-label">Department Name</label>
					<div class="js-dep-name t-sub-label"></div>
				</div>
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Email</label>
					<div class="js-dep-email t-sub-label"></div>
				</div>
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Description</label>
					<div class="js-dep-desc t-sub-label"></div>
				</div>			
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Status</label>
					<div class="js-dep-status t-sub-label"></div>
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
		<form id="frmEditDepartment" method="post" action="" class="row form-horizontal">
            @csrf
			<div class="col-md-12">
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Department Name</label>
                    <input class="form-control form-control-sm" name="dep_name" type="text" autocomplete="off">
				</div>
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Email</label>
                    <input class="form-control form-control-sm" name="dep_email" type="email" autocomplete="off">
				</div>
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Description</label>
					<textarea  class="form-control form-control-sm" name="dep_desc" rows="2" autocomplete="off"></textarea>
				</div>			
				<div class="form-group mb-2">
					<label class="mb-1 t-main-label">Status</label>
					<select name="dep_status"  class="form-control form-control-sm">
						<option value="I">Inoperative</option>
						<option value="O">Operative</option>
					</select>
				</div>			
				<div class="row justify-content-end mt-3">
					<div class="col-auto">
						<div class="form-group foot mb-0">
							<input type="hidden" name="enc_depid">
							<button type="button" class="btn btn-sm mr-1 t-close-btn" onclick="javascript:closeEditRecord()">Close</button>
							<button type="submit" class="btn btn-sm t-save-btn">Save</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>   
@endsection