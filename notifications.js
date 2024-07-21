document.addEventListener('DOMContentLoaded', function() {
    function checkForNotifications() {
        fetch('check_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    data.forEach(notification => {
                        // Request permission if not granted
                        if (Notification.permission === 'granted') {
                            new Notification(`New Schedule: ${notification.date}`, {
                                body: `${notification.location} - ${notification.type}`
                            });
                        } else if (Notification.permission !== 'denied') {
                            Notification.requestPermission().then(permission => {
                                if (permission === 'granted') {
                                    new Notification(`New Schedule: ${notification.date}`, {
                                        body: `${notification.location} - ${notification.type}`
                                    });
                                }
                            });
                        }
                    });
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    // Check for notifications every 3 minute
    setInterval(checkForNotifications, 300000);
});
