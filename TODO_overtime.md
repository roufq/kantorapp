# TODO: Implement Overtime Request and Approval System

## Database Migrations
- [x] Create migration for overtime_requests table (id, user_id, date, start_time, end_time, duration_hours, reason, status, selected_masters)
- [x] Create migration for overtime_approvals table (id, overtime_request_id, master_id, status, approved_at, notes)

## Models
- [x] Create Overtime model with relationships to User and OvertimeApproval
- [x] Create OvertimeApproval model with relationships

## Controllers
- [x] Create OvertimeController with CRUD methods and approval logic

## Views
- [x] Create overtime/index.blade.php for listing requests
- [x] Create overtime/create.blade.php for submitting requests
- [x] Create overtime/show.blade.php for viewing details

## Routes
- [x] Add routes for overtime functionality in web.php

## Followup
- [x] Run migrations
- [ ] Test the functionality
