@extends('layouts.appnew')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
<style>
  /* Executive Soft Calendar Aesthetics */
  #calendar { 
    min-height: 800px; 
    background: #ffffff; 
    border-radius: 32px; 
    padding: 30px; 
    border: 1px solid rgba(0,0,0,0.03);
    box-shadow: 0 20px 50px rgba(0,0,0,0.02);
  }
  
  .fc-unthemed th, .fc-unthemed td, .fc-unthemed thead, .fc-unthemed tbody, .fc-unthemed .fc-divider, .fc-unthemed .fc-row, .fc-unthemed .fc-content, .fc-popover {
    border-color: #f8fafc !important;
  }

  .fc-day-header {
    background: transparent !important;
    color: var(--text-muted) !important;
    padding: 20px 0 !important;
    text-transform: uppercase;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    border: none !important;
    border-bottom: 1px solid #f1f5f9 !important;
  }

  .fc-day-number {
    color: #94a3b8 !important;
    font-weight: 800;
    padding: 15px !important;
    font-size: 0.9rem;
  }

  .fc-unthemed td.fc-today {
    background-color: var(--soft-mint) !important;
    border-top: 3px solid var(--primary) !important;
  }

  /* Toolbar styling */
  .fc-button {
    background: #ffffff !important;
    color: var(--text-main) !important;
    border: 1px solid #f1f5f9 !important;
    box-shadow: 0 4px 6px rgba(0,0,0,0.02) !important;
    border-radius: 14px !important;
    padding: 8px 20px !important;
    font-weight: 700 !important;
    height: auto !important;
    text-transform: capitalize !important;
    transition: all 0.2s ease !important;
    outline: none !important;
  }
  .fc-button:hover { background: #f8fafc !important; transform: translateY(-1px); }
  .fc-state-active { background: var(--primary) !important; color: #fff !important; border-color: var(--primary) !important; }

  /* Event Styling */
  .fc-event {
    border: none !important;
    border-radius: 12px !important;
    padding: 8px 12px !important;
    margin: 3px 6px !important;
    font-weight: 700 !important;
    font-size: 0.75rem !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04) !important;
    transition: all 0.2s ease;
  }
  .fc-event:hover { transform: scale(1.02); box-shadow: 0 6px 15px rgba(0,0,0,0.06) !important; }

  .fc-event-on { background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%) !important; color: #fff !important; }
  .fc-event-off { background: var(--soft-lavender, #f5f3ff) !important; color: var(--soft-lavender-text, #5b21b6) !important; }
  .fc-event-leave { background: var(--soft-rose) !important; color: var(--soft-rose-text) !important; }
  .fc-event-missing { background: #ffffff !important; color: #cbd5e1 !important; border: 2px dashed #f1f5f9 !important; box-shadow: none !important; }

  .glass-filter {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
  }

  .note-pill {
    background: #ffffff;
    border-left: 4px solid var(--primary);
    padding: 15px 20px;
    border-radius: 16px;
    margin-bottom: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--text-muted);
  }
</style>
@endpush

@section('content')
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-end">
        <div class="col-lg-7">
          <div class="d-flex align-items-center mb-2">
            <div class="rounded-4 bg-primary bg-opacity-10 p-2 me-3">
                <i class="mdi mdi-calendar-multiselect fs-3 text-primary"></i>
            </div>
            <h1 class="fw-800 mb-0" style="font-size: clamp(1.8rem, 4vw, 2.8rem); background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
              {{ __('Operational Roster') }}
            </h1>
          </div>
          <p class="text-muted mb-0 ps-1" style="font-size: 1.1rem; opacity: 0.7;">{{ __('Advanced visual intelligence for workforce scheduling and availability metrics.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="{{ route('shifts.rosters.index') }}" class="btn btn-white border-0 rounded-pill px-4 py-3 fw-bold shadow-soft">
            <i class="mdi mdi-format-list-bulleted me-2 text-primary"></i>{{ __('Go to Roster Directory') }}
          </a>
        </div>
      </div>

      <div class="card mb-4 border-0 shadow-soft rounded-5 glass-filter">
        <div class="card-body p-4">
          <form method="GET" action="{{ route('shifts.rosters.calendar') }}" class="row g-4 align-items-end">
            <div class="col-md-5">
              <label class="form-label text-muted smaller fw-bold text-uppercase ms-1 mb-2">{{ __('Select Operational Site') }}</label>
              <div class="input-group rounded-pill overflow-hidden border border-light shadow-sm">
                <span class="input-group-text bg-white border-0 ps-3"><i class="mdi mdi-map-marker-radius text-primary"></i></span>
                <select name="location_id" class="form-select border-0 bg-white fw-bold shadow-none" onchange="this.form.submit()">
                  @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" @if($loc->id == $locationId) selected @endif>{{ $loc->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-5">
              <label class="form-label text-muted smaller fw-bold text-uppercase ms-1 mb-2">{{ __('Timeline Navigation') }}</label>
              <div class="input-group rounded-pill overflow-hidden border border-light shadow-sm">
                <span class="input-group-text bg-white border-0 ps-3"><i class="mdi mdi-calendar-search text-primary"></i></span>
                <input type="date" name="week_start" class="form-control border-0 bg-white fw-bold shadow-none" value="{{ $focusDate->toDateString() }}" onchange="this.form.submit()">
              </div>
            </div>
            <div class="col-md-2 text-md-end">
                <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-bold shadow-soft">
                    {{ __('Sync View') }}
                </button>
            </div>
          </form>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-3">
          <div class="card p-4 border-0 shadow-soft rounded-5 h-100" style="background: linear-gradient(180deg, #f5f3ff 0%, #ffffff 100%);">
            <div id="calendar-notes-panel">
                <h6 class="fw-800 mb-4 text-dark d-flex align-items-center"><i class="mdi mdi-information-outline me-2 text-primary fs-4"></i>{{ __('Calendar Intelligence') }}</h6>
                <div class="fc-external-list">
                <div class="note-pill">Schedules are synced per location.</div>
                <div class="note-pill" style="border-left-color: #f43f5e;">Check special permits in red.</div>
                <div class="note-pill" style="border-left-color: #0ea5e9;">Holidays are marked automatically.</div>
                </div>
            </div>

            <div id="personnel-pulse-panel" style="display: none;">
                <h6 class="fw-800 mb-2 text-dark d-flex align-items-center"><i class="mdi mdi-account-group-outline me-2 text-primary fs-4"></i>{{ __('Personnel Pulse') }}</h6>
                <p class="smaller text-muted mb-4" id="pulse-date-label">Selected Date</p>
                <div id="pulse-list" class="d-flex flex-column gap-2">
                    <!-- Dynamic List -->
                </div>
                <button class="btn btn-link btn-sm mt-3 p-0 text-primary fw-bold" onclick="resetPulseView()">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Intelligence
                </button>
            </div>
            
            <div class="mt-auto pt-4 border-top border-light border-opacity-50">
              <div class="smaller fw-bold text-muted text-uppercase mb-3 letter-spacing-1">{{ __('LEGEND') }}</div>
              <div class="d-flex flex-column gap-3">
                <div class="legend-item"><span class="legend-dot" style="background:#0ea5e9"></span> Active Shift</div>
                <div class="legend-item"><span class="legend-dot" style="background:#f5f3ff; border: 1px solid #ddd;"></span> OFF Day</div>
                <div class="legend-item"><span class="legend-dot" style="background:#fff1f2"></span> Leave / Permit</div>
                <div class="legend-item"><span class="legend-dot" style="background:#ffffff; border:1px dashed #cbd5e1"></span> No Schedule</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-9">
          <div id="calendar"></div>
        </div>
      </div>
<!-- Shift Details Modal -->
<div class="modal fade" id="shiftDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-5 overflow-hidden">
      <div class="modal-header border-0 p-4 pb-0">
        <h5 class="fw-800 text-dark mb-0 d-flex align-items-center">
            <i class="mdi mdi-account-circle-outline me-2 text-primary fs-3"></i>
            <span id="modal-employee-name">Employee Name</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="d-flex flex-column gap-3">
            <div class="p-3 bg-light rounded-4 border border-light">
                <div class="smaller text-muted fw-bold text-uppercase mb-1">{{ __('Active Timeline') }}</div>
                <div class="h6 mb-0 d-flex align-items-center">
                    <i class="mdi mdi-clock-outline me-2 text-primary"></i>
                    <span id="modal-shift-time" class="fw-bold">08:00 - 17:00</span>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-6">
                    <div class="p-3 bg-light rounded-4 border border-light h-100">
                        <div class="smaller text-muted fw-bold text-uppercase mb-1">{{ __('Operation') }}</div>
                        <div id="modal-shift-name" class="fw-bold text-dark">Office Shift</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-light rounded-4 border border-light h-100">
                        <div class="smaller text-muted fw-bold text-uppercase mb-1">{{ __('Status') }}</div>
                        <span id="modal-status-badge" class="badge rounded-pill bg-primary px-3">Active</span>
                    </div>
                </div>
            </div>

            <div id="modal-notes-container" class="p-3 bg-light rounded-4 border border-light">
                <div class="smaller text-muted fw-bold text-uppercase mb-1">{{ __('System Notes') }}</div>
                <div id="modal-notes" class="smaller fw-medium">No additional remarks.</div>
            </div>
        </div>
      </div>
      <div class="modal-footer border-0 p-4 pt-0">
        <button type="button" class="btn btn-primary w-100 rounded-pill py-2 fw-bold" data-bs-dismiss="modal">Close Briefing</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
<script>
  $(function() {
    var calendarEvents = @json($calendarEvents);
    if (typeof moment !== 'undefined') moment.locale('en');

    if ($.isFunction($.fn.fullCalendar)) {
      $('#calendar').fullCalendar({
        locale: 'en',
        header: { left: 'prev,next today', center: 'title', right: 'month,agendaWeek' },
        defaultDate: '{{ $focusDate->toDateString() }}',
        editable: false,
        eventLimit: true,
        height: 850,
        contentHeight: 'auto',
        events: calendarEvents,
        dayClick: function(date, jsEvent, view) {
            $('.fc-day').removeClass('bg-primary bg-opacity-10');
            $(this).addClass('bg-primary bg-opacity-10');
            updatePersonnelPulse(date.format('YYYY-MM-DD'));
        },
        eventClick: function(event) {
            showShiftDetail(event);
            updatePersonnelPulse(event.start.format('YYYY-MM-DD'));
        },
        eventRender: function(event, element) {
          if (event.time && event.status !== 'missing') {
            element.find('.fc-title').append('<div style="font-size:0.7rem; font-weight: 500; margin-top: 4px; opacity:0.9; color: inherit;">' + event.time + '</div>');
          }
          if (event.notes) {
              element.attr('title', event.notes);
          }
        },
        viewRender: function(view, element) {
            $('.fc-center h2').addClass('fw-800 text-dark small-caps').css('font-size', '1.4rem');
        }
      });
    }
  });

  function updatePersonnelPulse(dateStr) {
    const events = @json($calendarEvents);
    const dayEvents = events.filter(e => e.start === dateStr && e.status !== 'missing');
    
    $('#calendar-notes-panel').hide();
    $('#personnel-pulse-panel').show();
    $('#pulse-date-label').text(moment(dateStr).format('dddd, MMMM Do YYYY'));
    
    let html = '';
    if (dayEvents.length === 0) {
        html = '<div class="text-center py-4 text-muted smaller">No active personnel assigned.</div>';
    } else {
        dayEvents.forEach(e => {
            const statusClass = e.status === 'on' ? 'bg-primary' : (e.status === 'off' ? 'bg-secondary' : 'bg-danger');
            html += `
                <div class="p-3 border rounded-4 bg-white shadow-sm border-light cursor-pointer" onclick="showShiftDetailFromPulse('${e.user_id}', '${e.start}')">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="fw-bold text-dark smaller">${e.user_name || 'Staff Member'}</span>
                        <span class="badge ${statusClass} rounded-pill" style="font-size: 0.6rem;">${e.status.toUpperCase()}</span>
                    </div>
                    <div class="smaller text-muted"><i class="mdi mdi-clock-outline me-1"></i>${e.time || 'N/A'}</div>
                </div>
            `;
        });
    }
    $('#pulse-list').html(html);
  }

  function showShiftDetail(event) {
    $('#modal-employee-name').text(event.user_name || 'Personnel');
    $('#modal-shift-time').text(event.time || 'N/A');
    $('#modal-shift-name').text(event.title.split('•')[1]?.trim() || 'Custom Shift');
    $('#modal-notes').text(event.notes || 'No additional remarks available for this assignment.');
    
    const statusMap = {
        'on': { text: 'ACTIVE ON DUTY', class: 'bg-primary' },
        'off': { text: 'OFF DUTY / LIBUR', class: 'bg-secondary' },
        'leave': { text: 'ON LEAVE / PERMIT', class: 'bg-danger' }
    };
    
    const statusData = statusMap[event.status] || { text: 'UNKNOWN', class: 'bg-dark' };
    $('#modal-status-badge').text(statusData.text).removeClass().addClass('badge rounded-pill px-3 ' + statusData.class);

    const detailModal = new bootstrap.Modal(document.getElementById('shiftDetailModal'));
    detailModal.show();
  }

  function showShiftDetailFromPulse(userId, date) {
    const events = @json($calendarEvents);
    const event = events.find(e => e.user_id == userId && e.start == date);
    if (event) showShiftDetail(event);
  }

  function resetPulseView() {
    $('#personnel-pulse-panel').hide();
    $('#calendar-notes-panel').show();
    $('.fc-day').removeClass('bg-primary bg-opacity-10');
  }
</script>
@endpush
