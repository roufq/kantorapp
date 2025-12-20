@extends('layouts.appnew')

@push('styles')
<link rel="stylesheet" href="{{ asset('NewAsset/assets/plugins/jquery-ui/jquery-ui.min.css') }}">
<link rel="stylesheet" href="{{ asset('NewAsset/assets/plugins/fullcalendar/css/fullcalendar.min.css') }}">
<style>
  #calendar { min-height: 700px; background: #fff; }
  .fc-external-list .fc-event {
    margin: 6px 0;
    padding: 10px 12px;
    background: #f7f9ff !important;
    border: 1px solid #c6ceff;
    color: #1c274c !important;
    cursor: default;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 700;
    opacity: 1 !important;
    box-shadow: inset 0 0 0 1px #e3e8ff;
  }
  .fc-event.fc-event-on { background: #0d6efd; border-color: #0d6efd; }
  .fc-event.fc-event-off { background: #6c757d; border-color: #6c757d; }
  .fc-event.fc-event-leave { background: #dc3545; border-color: #dc3545; }
  .fc-event.fc-event-missing {
    background: #fdf2e9;
    border: 1px dashed #f0ad4e;
    color: #8a6d3b !important;
  }
  .fc-event.fc-event-missing .fc-title,
  .fc-event.fc-event-missing .fc-time {
    color: #8a6d3b !important;
  }
  .fc-notes { font-size: 12px; color: #6c757d; margin-top: 2px; }
  .legend-dot { width: 12px; height: 12px; display: inline-block; border-radius: 50%; margin-right: 6px; }
  .legend-row { display: flex; gap: 14px; align-items: center; flex-wrap: wrap; margin-top: 8px; }
  .legend-item { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; color: #1f2a44; }
</style>
@endpush

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1 class="h4 mb-1">Kalender Shift (Rosters)</h1>
    <p class="text-muted mb-0">Lihat jadwal shift per lokasi dalam tampilan kalender penuh.</p>
  </div>
  <a href="{{ route('shifts.rosters.index') }}" class="text-decoration-none">Kembali ke daftar roster</a>
</div>

<div class="card">
  <div class="card-body">
    <form method="GET" action="{{ route('shifts.rosters.calendar') }}" class="row g-2 align-items-end">
      @if($locations->count() > 0)
        <div class="col-md-4">
          <label class="form-label">Lokasi</label>
          <select name="location_id" class="form-control" onchange="this.form.submit()">
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" @if($loc->id == $locationId) selected @endif>{{ $loc->name }} ({{ $loc->code }})</option>
            @endforeach
          </select>
        </div>
      @endif
      <div class="col-md-4">
        <label class="form-label">Tampilkan bulan dari</label>
        <input type="date" name="week_start" class="form-control" value="{{ $focusDate->toDateString() }}" onchange="this.form.submit()">
      </div>
    </form>

    <div class="legend-row">
      <span class="legend-item"><span class="legend-dot" style="background:#0d6efd"></span>Jadwal aktif</span>
      <span class="legend-item"><span class="legend-dot" style="background:#6c757d"></span>OFF</span>
      <span class="legend-item"><span class="legend-dot" style="background:#dc3545"></span>Cuti/Izin</span>
      <span class="legend-item"><span class="legend-dot" style="background:#f8f9fa; border:1px dashed #ced4da"></span>Belum ada jadwal</span>
    </div>

    <div class="row mt-3">
      <div class="col-lg-3 col-md-4 mb-3">
        <h6 class="mb-2">Catatan</h6>
        <div class="fc-external-list">
          <div class="fc-event">Shift aktif akan muncul per hari sesuai roster.</div>
          <div class="fc-event">Cuti/Izin ditandai merah.</div>
          <div class="fc-event">Tanggal tanpa jadwal diberi label “Belum ada jadwal”.</div>
        </div>
      </div>
      <div class="col-lg-9 col-md-8">
        <div id="calendar"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('NewAsset/assets/plugins/moment/moment.js') }}"></script>
<script src="{{ asset('NewAsset/assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('NewAsset/assets/plugins/fullcalendar/js/fullcalendar.min.js') }}"></script>
<script>
  $(function() {
    var calendarEvents = @json($calendarEvents);
    if (typeof moment !== 'undefined') {
      moment.locale('id');
    }

    if ($.isFunction($.fn.fullCalendar)) {
      $('#calendar').fullCalendar({
        locale: 'id',
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,agendaWeek,agendaDay'
        },
        defaultDate: '{{ $focusDate->toDateString() }}',
        editable: false,
        eventLimit: true,
        height: 750,
        events: calendarEvents,
        eventRender: function(event, element) {
          var cls = Array.isArray(event.className) ? event.className.join(' ') : (event.className || '');
          if (event.time && cls.indexOf('fc-event-missing') === -1) {
            element.find('.fc-title').append('<div class="small">' + event.time + '</div>');
          }
          if (event.notes) {
            element.append('<div class="fc-notes">' + event.notes + '</div>');
          }
        }
      });
    } else {
      $('#calendar').html('<div class="alert alert-warning">Plugin FullCalendar tidak termuat.</div>');
    }
  });
</script>
@endpush
