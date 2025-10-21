# TODO: Decouple Shifts from Locations - Multi-Location Shift Schedule Implementation

## Database Migrations
- [x] Create migration to drop location_id from shifts table and add schedule_type enum to locations
- [x] Create migration for location_shifts pivot table for many-to-many relationship
- [x] Create migration to add daily_schedule JSON field to locations

## Model Updates
- [x] Update Shift model to remove location relationship and add belongsToMany locations
- [x] Update Location model to add schedule_type enum, daily_schedule JSON, and belongsToMany shifts

## Controller Updates
- [x] Update ShiftController to remove location filtering and validation
- [x] Update LocationController to handle schedule types and daily schedule management

## View Updates
- [x] Update shift views (create/edit) to remove location selection
- [x] Update location views (create/edit) to add schedule type selection and daily schedule input

## Testing & Validation
- [x] Run migrations to update database structure
- [x] Test shift creation without location requirement
- [x] Test location schedule type selection and daily schedule input
- [x] Verify many-to-many relationship works for location-shift assignments
- [x] Update any dependent controllers/views that relied on old structure
