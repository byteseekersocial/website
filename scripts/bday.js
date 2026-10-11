    // --- 1. Birthday about me script ---  
    // Define the exact date (e.g., January 1, 2010)
    const exactDate = new Date('2007-03-04');

    // Get the current date
    const currentDate = new Date();

    // Calculate the difference in years
    let years = currentDate.getFullYear() - exactDate.getFullYear();

    // Adjust for cases where the current month/day is before the exact date's month/day
    if (currentDate.getMonth() < exactDate.getMonth() ||
        (currentDate.getMonth() === exactDate.getMonth() && currentDate.getDate() < exactDate.getDate())) {
        years--;
    }

    // Get the HTML element and update its content
    document.getElementById('yearsAgo').textContent = years;
