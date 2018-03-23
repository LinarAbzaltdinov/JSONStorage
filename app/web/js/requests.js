function updateJSON(url, newData) {
    $.ajax({
        type: "PUT",
        url: url,
        data: {text: newData},
        dataType: "json",
        success: function (data) {
            if (!data.status) {
                alert(data.errorMessage);
            } else {
                alert("OK");
            }
        },
        failure: function (errMsg) {
            alert(errMsg);
        }
    });
}

function deleteJSON(url, redirectTo) {
    $.ajax({
        type: "DELETE",
        url: url,
        success: function (data) {
            if (data.status) {
                window.location.href = redirectTo;
            } else {
                alert(data.errorMessage);
            }
        },
        failure: function (errMsg) {
            alert(errMsg);
        }
    });
}