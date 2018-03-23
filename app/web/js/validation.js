function isValidJSON(string) {
    try {
        JSON.parse(string);
    } catch (e) {
        return false;
    }
    return true;
}

function isValidDateTime(minutes, hours, day, month, year) {
    var date = new Date(year, month, day, hours, minutes);
    //check if autoincreased
    if ((date.getMinutes() != minutes)
        || (date.getHours() != hours)
        || (date.getDate() != day)
        || (date.getMonth() != month)
        || (date.getFullYear() != year)) {
        return false;
    }
    if (date < Date.now()) {
        return false;
    }
    var timeDiff = Math.abs(date.getTime() - Date.now().getTime());
    var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
    if (diffDays > 365) {
        return false;
    }
    return true;
}