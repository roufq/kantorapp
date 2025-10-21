# TODO: Remove shift_id from employees table and related CRUD

## Steps to Complete

- [x] Create new migration to drop shift_id column from employees table
- [x] Update Employee model: remove shift_id from fillable and remove shift relationship
- [x] Update EmployeeController: remove shift_id validation, remove shift loading in index/show, remove shift_id from store/update
- [x] Update resources/views/karyawans/create.blade.php: remove shift fields
- [x] Update resources/views/karyawans/edit.blade.php: remove shift fields
- [x] Update resources/views/karyawans/index.blade.php: remove shift column
- [x] Update resources/views/karyawans/show.blade.php: remove shift display
- [x] Run the new migration
- [x] Test employee CRUD to ensure it works without shift_id
