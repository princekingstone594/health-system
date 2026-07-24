<h2>Set Availability</h2>

<form method="POST">
    @csrf

    <select name="day">
        <option>Monday</option>
        <option>Tuesday</option>
        <option>Wednesday</option>
        <option>Thursday</option>
        <option>Friday</option>
    </select>

    <input type="time" name="start_time">
    <input type="time" name="end_time">

    <button>Add</button>
</form>

<hr>

@foreach($availabilities as $a)
    <p>{{ $a->day }}: {{ $a->start_time }} - {{ $a->end_time }}</p>
@endforeach