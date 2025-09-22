<!DOCTYPE html>
<html>
<head>
    <title>Setup Dashboard</title>
</head>
<body>
    <h1>Initial Setup</h1>
    <form action="{{ route('setup.store') }}" method="POST">
        @csrf
        <div>
            <label>Property Name:</label>
            <input type="text" name="floors[0][property_name]" required>
            <label>Floor Name:</label>
            <input type="number" name="floors[0][floor_name]" required>
        </div>
        <button type="submit">Save Configuration</button>
    </form>
	
	
	
</body>
</html>
