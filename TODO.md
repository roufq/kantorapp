# TODO: Add File Upload/Download to Master Tasks and Employee Tasks

## Database Migrations
- [ ] Create migration to add photo_path and document_path to master_tasks table
- [ ] Create migration to add photo_path and document_path to employee_tasks table

## Models
- [ ] Update MasterTask model to include photo_path and document_path in fillable
- [ ] Update Task model to include photo_path and document_path in fillable

## Controllers
- [ ] Update MasterTaskController store/update methods to handle file uploads
- [ ] Update TaskController store/update methods to handle file uploads
- [ ] Add download methods to both controllers for photo and document

## Views
- [ ] Update master-tasks/create.blade.php to include file input fields
- [ ] Update master-tasks/edit.blade.php to include file input fields and display current files
- [ ] Update master-tasks/show.blade.php to include download links
- [ ] Update tasks/create.blade.php to include file input fields
- [ ] Update tasks/edit.blade.php to include file input fields and display current files
- [ ] Update tasks/show.blade.php to include download links

## Routes
- [ ] Add download routes for both master-tasks and tasks

## Followup
- [ ] Run migrations
- [ ] Ensure storage link is set up
- [ ] Test functionality
