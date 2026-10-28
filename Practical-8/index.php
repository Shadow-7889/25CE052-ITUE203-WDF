<?php
require_once "db.php";
$message = "";
$messageType = "success";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "add_student") {
        $name = trim($_POST["name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $course = trim($_POST["course"] ?? "");

        if ($name === "" || $email === "" || $course === "") {
            $message = "All student fields are required.";
            $messageType = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Enter a valid student email.";
            $messageType = "error";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO students (name, email, course)
                 VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $name, $email, $course);

            if ($stmt->execute()) {
                $message = "Student added successfully!";
            } else {
                $message = ($stmt->errno === 1062)
                    ? "This email is already registered."
                    : "Unable to add student.";
                $messageType = "error";
            }
            $stmt->close();
        }
    }

    elseif ($action === "add_event") {
        $eventName = trim($_POST["event_name"] ?? "");
        $eventDate = trim($_POST["event_date"] ?? "");
        $venue = trim($_POST["venue"] ?? "");
        $date = DateTime::createFromFormat("!Y-m-d", $eventDate);
        $validDate = $date && $date->format("Y-m-d") === $eventDate;

        if ($eventName === "" || $venue === "" || !$validDate) {
            $message = "Enter a valid event name, date, and venue.";
            $messageType = "error";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO events (event_name, event_date, venue)
                 VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $eventName, $eventDate, $venue);

            if ($stmt->execute()) {
                $message = "Event added successfully!";
            } else {
                $message = "Unable to add event.";
                $messageType = "error";
            }
            $stmt->close();
        }
    }

    elseif ($action === "register_event") {
        $studentId = filter_var(
            $_POST["student_id"] ?? "",
            FILTER_VALIDATE_INT
        );
        $eventId = filter_var(
            $_POST["event_id"] ?? "",
            FILTER_VALIDATE_INT
        );

        if (!$studentId || !$eventId || $studentId < 1 || $eventId < 1) {
            $message = "Please select a student and an event.";
            $messageType = "error";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO registrations (student_id, event_id)
                 VALUES (?, ?)"
            );
            $stmt->bind_param("ii", $studentId, $eventId);

            if ($stmt->execute()) {
                $message = "Student registered for the event successfully!";
            } elseif ($stmt->errno === 1062) {
                $message = "This student is already registered for this event.";
                $messageType = "error";
            } else {
                $message = "Registration failed. Check the selected student and event.";
                $messageType = "error";
            }
            $stmt->close();
        }
    }
}

$students = $conn->query(
    "SELECT student_id, name, email, course
     FROM students ORDER BY student_id DESC"
);

$events = $conn->query(
    "SELECT event_id, event_name, event_date, venue
     FROM events ORDER BY event_date DESC"
);

$registrations = $conn->query(
    "SELECT r.registration_id, s.name AS student_name,
            e.event_name, e.event_date, r.registration_date
     FROM registrations r
     INNER JOIN students s ON r.student_id = s.student_id
     INNER JOIN events e ON r.event_id = e.event_id
     ORDER BY r.registration_id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Database Management</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f5f9;
            margin: 0;
            padding: 25px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        section {
            background: white;
            padding: 22px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ddd;
            overflow-x: auto;
        }

        h1, h2 {
            color: #243b55;
        }

        label {
            display: block;
            margin-top: 12px;
        }

        input, select {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }

        button {
            margin-top: 15px;
            padding: 11px 18px;
            border: none;
            border-radius: 5px;
            background: #2463eb;
            color: white;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #e8eefc;
        }

        .success {
            background: #e4f7e9;
            color: #176b36;
            padding: 12px;
        }

        .error {
            background: #fde8e8;
            color: #a61b1b;
            padding: 12px;
        }
    </style>
</head>
<body>
<div class="container">

    <h1>StudentHub Database Management</h1>

    <?php if ($message !== ""): ?>
        <p class="<?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>
    <section>
        <h2>1. Add Student</h2>

        <form method="POST">
            <input type="hidden" name="action" value="add_student">

            <label for="name">Student Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="course">Course</label>
            <input type="text" id="course" name="course" required>

            <button type="submit">Add Student</button>
        </form>
    </section>

    <section>
        <h2>2. Add Event</h2>

        <form method="POST">
            <input type="hidden" name="action" value="add_event">

            <label for="event_name">Event Name</label>
            <input type="text" id="event_name" name="event_name" required>

            <label for="event_date">Event Date</label>
            <input type="date" id="event_date" name="event_date" required>

            <label for="venue">Venue</label>
            <input type="text" id="venue" name="venue" required>

            <button type="submit">Add Event</button>
        </form>
    </section>

    <section>
        <h2>3. Register Student for an Event</h2>

        <form method="POST">
            <input type="hidden" name="action" value="register_event">

            <label for="student_id">Select Student</label>
            <select id="student_id" name="student_id" required>
                <option value="">Choose a student</option>
                <?php while ($s = $students->fetch_assoc()): ?>
                    <option value="<?= (int)$s["student_id"] ?>">
                        <?= htmlspecialchars($s["name"]) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="event_id">Select Event</label>
            <select id="event_id" name="event_id" required>
                <option value="">Choose an event</option>
                <?php while ($e = $events->fetch_assoc()): ?>
                    <option value="<?= (int)$e["event_id"] ?>">
                        <?= htmlspecialchars($e["event_name"]) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Register for Event</button>
        </form>
    </section>

    <section>
        <h2>4. Student Records</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
            </tr>

            <?php while ($s = $students->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$s["student_id"] ?></td>
                    <td><?= htmlspecialchars($s["name"]) ?></td>
                    <td><?= htmlspecialchars($s["email"]) ?></td>
                    <td><?= htmlspecialchars($s["course"]) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </section>

    <section>
        <h2>5. Event Records</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Event</th>
                <th>Date</th>
                <th>Venue</th>
            </tr>

            <?php while ($e = $events->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$e["event_id"] ?></td>
                    <td><?= htmlspecialchars($e["event_name"]) ?></td>
                    <td><?= htmlspecialchars($e["event_date"]) ?></td>
                    <td><?= htmlspecialchars($e["venue"]) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </section>

    <section>
        <h2>6. Event Registrations</h2>

        <table>
            <tr>
                <th>Registration ID</th>
                <th>Student</th>
                <th>Event</th>
                <th>Event Date</th>
                <th>Registered At</th>
            </tr>

            <?php while ($r = $registrations->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$r["registration_id"] ?></td>
                    <td><?= htmlspecialchars($r["student_name"]) ?></td>
                    <td><?= htmlspecialchars($r["event_name"]) ?></td>
                    <td><?= htmlspecialchars($r["event_date"]) ?></td>
                    <td><?= htmlspecialchars($r["registration_date"]) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </section>
</div>
</body>
</html>
