function openSeatPickerModal() {
    var el = document.getElementById('event_id');
    var eid = el ? el.value : '';
    if (!eid) {
        alert('Select an Event first.');
        return;
    }
    window.open(
        'index.php?module=ev_EventSeats&action=SeatPicker&event_id=' + encodeURIComponent(eid),
        'SeatPicker',
        'width=960,height=680,scrollbars=yes'
    );
}