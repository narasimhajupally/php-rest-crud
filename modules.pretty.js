let urlInput = document.getElementById("url");
let requestTypeSelect = document.querySelector("#requestType");
let bodyInput = document.getElementById('requestJsonText');
let responseContainer = document.getElementById('JSONresponse');

let reqs = [
    {
        type: 'GET',
        uri: 'module/list',
        body: 'NA',
    },
    {
        type: 'POST',
        uri: 'module/create',
        body: JSON.stringify({ code: '123456-ABCD123', title: 'module title' }, null, 4),
    },
    {
        type: 'GET',
        uri: 'module/{m_id}/assessment/list',
        body: 'NA',
    },
    {
        type: 'POST',
        uri: 'module/{m_id}/assessment/create',
        body: JSON.stringify({ type: 'classtest', description: 'assessment description', weight: 30 }, null, 4),
    },
    {
        type: 'PUT',
        uri: 'module/{m_id}/assessment/{a_id}/update',
        body: JSON.stringify({ type: 'performance', description: 'update assessment description', weight: 10 }, null, 4),
    },
    {
        type: 'DELETE',
        uri: 'assessment/{a_id}/delete',
        body: 'NA',
    }
]

function setRequestTemplate(index) {
    urlInput.value = window.location.origin + '/~sgschene/v1/' + reqs[index].uri;
    requestTypeSelect.value = reqs[index].type;
    bodyInput.value = reqs[index].body;
}

let submit = document.getElementById('submit');
submit.addEventListener('click', async () => {
    responseContainer.innerText = "Fetching response...";

    let url = urlInput.value;
    let requestType = requestTypeSelect.value;
    let requestData = bodyInput.value;

    if (requestType == 'GET' || requestType == 'DELETE') {
        const response = await fetch(url, { method: requestType });
        let data = await response.json();
        responseContainer.innerText = response.status + ' ' + response.statusText + '\n' + JSON.stringify(data, null, 4);
    } else if (requestType == 'POST' || requestType == 'PUT') {
        const response = await fetch(url, {
            method: requestType,
            body: requestData,
            headers: {
                "Content-type": "application/json; charset=UTF-8"
            }
        })
        let data = await response.json();
        responseContainer.innerText = response.status + ' ' + response.statusText + '\n' + JSON.stringify(data, null, 4);
    }
});