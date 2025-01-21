@extends('admin.layouts.app')
   @section('content')
	 	<!-- Content Header (Page header) -->
         <section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Edit Sub Category</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{route('sub-categories.index')}}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">

					<form action="" method="post" id="subCategoryForm" name="subCategoryForm">
						<div class="card">
							<div class="card-body">								
								<div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="Category">Category</label>
                                            <select name="category" id="category" class="form-control">
                                            <option value="">Select a category</option>
                                            @if($categories->isNotEmpty())
                                                @foreach($categories as $category)
                                                <option {{ ($subcategory->category_id == $category->id) ? 'selected' : '' }} value="{{$category->id}}">{{$category->name}} </option>
                                                @endforeach
                                                @endif
                                                </select>
                                                <p></p>
                                        </div>
                                    </div>

									<div class="col-md-6">
										<div class="mb-3">
											<label for="name">Name</label>
											<input type="text" value="{{ $subcategory->name }}" name="name" id="name" class="form-control" placeholder="Name">	
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="Slug">Slug</label>
											<input type="text" readonly name="slug" value="{{ $subcategory->slug }}" id="slug" class="form-control" placeholder="Slug">
											<p></p>	
										</div>
									</div>	
                                
                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="status">Status</label>
											<select name="status" id="status" class="form-control">
                                            <option {{ ($subcategory->status == 1) ? 'selected' : '' }} value="1">Active</option>
                                            <option {{ ($subcategory->status == 0) ? 'selected' : '' }} value="0">Block</option></select>
										</div>
									</div>												
								</div>
							</div>							
						</div>
						<div class="pb-5 pt-3">
							<button type="submit" class="btn btn-primary">Update</button>
							<a href="{{route('sub-categories.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
						</div>
                        </form>
					</div>
					<!-- /.card -->
				</section>
				<!-- /.content -->
			
                @endsection
   
@section('customJs')
   <script>
   $("#subCategoryForm").submit(function(event){
       event.preventDefault();
       var element = $(this);
       $("button[type=submit]").prop('disable',true);
       $.ajax({
           url: '{{ route("sub-categories.update", $subcategory->id) }}',
           type: 'put',
           data: element.serializeArray(), // Removed the semicolon here
           dataType: 'json',
		   
           success: function(response){
            $("button[type=submit]").prop('disable',false); 

		       if(response["status"] ==true){
               window.location.href="{{ route('sub-categories.index') }}";

				$("#name").removeClass('is-invalid')
                       .siblings('p')
                       .removeClass('invalid-feedback').html("");
			   $("#slug").removeClass('is-invalid')
                       .siblings('p')
                       .removeClass('invalid-feedback').html("");
			   }

              else{
                if(response['notFound']==true){
                    window.location.href="{{ route('sub-categories.index') }}";
                }
				var errors = response['errors'];
               if(errors['name']){
                   $("#name").addClass('is-invalid')
                       .siblings('p')
                       .addClass('invalid-feedback').html(errors['name']);
               } else {
                   $("#name").removeClass('is-invalid')
                       .siblings('p')
                       .removeClass('invalid-feedback').html("");
               }

               if(errors['slug']){
                   $("#slug").addClass('is-invalid')
                       .siblings('p')
                       .addClass('invalid-feedback').html(errors['slug']);
               } else {
                   $("#slug").removeClass('is-invalid')
                       .siblings('p')
                       .removeClass('invalid-feedback').html("");
               }
			  }
           },
           error: function(jqXHR, exception){
               console.log("Something went wrong");
           }
       });
   });

   $("#name").change(function(){
    element = $(this);
    $("button[type=submit]").prop('disable',true);
    $.ajax({
        url: '{{ route("getSlug") }}',
        type: 'get',
        data: {title: element.val()},
        dataType: 'json',
        success: function(response){
            $("button[type=submit]").prop('disable',false);
            if(response.status){
                $("#slug").val(response.slug);
            }
        }
    });
});

Dropzone.autoDiscover = false;    
const dropzone = $("#image").dropzone({ 
    init: function() {
        this.on('addedfile', function(file) {
            if (this.files.length > 1) {
                this.removeFile(this.files[0]);
            }
        });
    },
url:  "{{ route('temp-images.create') }}",
    maxFiles: 1,
    paramName: 'image',
    addRemoveLinks: true,
    acceptedFiles: "image/jpeg,image/png,image/gif",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }, success: function(file, response){
        $("#image_id").val(response.image_id);
        //console.log(response)
    }
});
   </script>

@endsection
