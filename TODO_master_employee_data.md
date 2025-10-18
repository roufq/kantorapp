# TODO: Implement Master User Retrieve Data from Employee

## Task Description
Allow master users to retrieve and view data from employees, specifically employee tasks assigned by the master.

## Requirements
- Add functionality for masters to view employee tasks they have assigned.
- Include search functionality by task title.
- Display tasks in a paginated list with status, due date, and assignee information.
- Allow masters to update task status directly from the view.
- Provide links to edit or delete tasks.

## Implementation Plan

### 1. Add Method to MasterTaskController
- Create `employeeTasksIndex(Request $request)` method in `MasterTaskController.php`
- Query master tasks where `assigned_by` is the current master user and `assigned_to` is an employee
- Include search functionality
- Paginate results
- Load relationships (assignee, assigner)

### 2. Add Route
- Add route `/master-tasks/employee-tasks` pointing to `employeeTasksIndex` method
- Apply `role:master` middleware

### 3. Verify View
- Ensure `resources/views/master-tasks/employee-tasks/index.blade.php` is correctly implemented
- View should display tasks with update status form, edit/delete links

## Files to Modify
- `app/Http/Controllers/MasterTaskController.php` - Add new method
- `routes/web.php` - Add new route

## Testing
- Test as master user: access employee tasks view
- Test search functionality
- Test status update
- Test edit/delete links
