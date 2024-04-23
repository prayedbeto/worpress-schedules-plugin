function loadOptions(teacherId) {
    let scheduleSelect = document.getElementById('horario_id');
    let scheduleSelectM = document.getElementById('m_horario_id');
    let scheduleSelectX = document.getElementById('x_horario_id');
    let scheduleSelectJ = document.getElementById('j_horario_id');
    let scheduleSelectV = document.getElementById('v_horario_id');
    scheduleSelect.innerHTML = ''  
    fetch('/wp-json/lavs-filter-options/v1/phrase?teacher_id=1?teacher_id=' + teacherId)
    .then(response => response.json())
    .then(data => {
        console.log(data);
        for(let i = 0; i < data.data.length; i++) {
            if(data.data[i].day == 'L')
                scheduleSelect.innerHTML = scheduleSelect.innerHTML + `<option value='${data.data[i].schedule_id}'>${data.data[i].schedule}</option>`
            if(data.data[i].day == 'M')
                scheduleSelectM.innerHTML = scheduleSelectM.innerHTML + `<option value='${data.data[i].schedule_id}'>${data.data[i].schedule}</option>`
            if(data.data[i].day == 'X')
                scheduleSelectX.innerHTML = scheduleSelectX.innerHTML + `<option value='${data.data[i].schedule_id}'>${data.data[i].schedule}</option>`
            if(data.data[i].day == 'J')
                scheduleSelectJ.innerHTML = scheduleSelectJ.innerHTML + `<option value='${data.data[i].schedule_id}'>${data.data[i].schedule}</option>`
            if(data.data[i].day == 'V')
                scheduleSelectV.innerHTML = scheduleSelectV.innerHTML + `<option value='${data.data[i].schedule_id}'>${data.data[i].schedule}</option>`
        }
    })
    .catch(error => {
        console.error('Error loading schedules:', error);
    });
}

function changeSchedules(event) {
    loadOptions(event.target.value)
} 

document.addEventListener("DOMContentLoaded", function(event) {
    let teacherSelect = document.getElementById('profesor_id')
    
    teacherSelect.addEventListener("change", changeSchedules);

    if(teacherSelect.value) {
        changeSchedules(teacherSelect.value)
    }
});