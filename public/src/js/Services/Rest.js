
const request = (endpoint, httpMethod, requestDatas, contentType, headers) => {

    contentType = contentType || 'json';
    headers = headers || false;

    return new Promise((resolve, reject) => {

        httpMethod = httpMethod || 'GET';
        requestDatas = requestDatas || {};
        let url = Config.apiUrl + endpoint;
        let options = {
            method: httpMethod,
            credentials: "same-origin"
        };
        if (headers) {
            options.headers = headers;
        }

        if (contentType === 'json') {
            if (Object.keys(requestDatas).length > 0) {
                options.body = JSON.stringify(requestDatas);
            }
        }
        else {
            options.body = requestDatas;
        }

        fetch(url, options).then((response) => {
            if (response.status !== 200 && response.status !== 201 && response.status !== 204) {
                response.text().then(
                    (text) => {
                        try {
                            reject(JSON.parse(text));
                        }
                        catch (error) {
                            reject(text);
                        }
                    },
                    (error) => {
                        reject(error);
                    }
                );
            }
            else {
                response.text().then(
                    (text) => {
                        try {
                            resolve(JSON.parse(text));
                        }
                        catch (error) {
                            resolve(text);

                        }
                    },
                    (error) => {
                        reject(error);
                    }
                );
            }
        }, reject).catch(reject);
    });
}

export default {

    authRequest: (endpoint, httpMethod, requestDatas, contentType) => {
        let headers = new Headers();
        let token = localStorage.getItem('token');
        headers.append('Authorization', 'Bearer ' + token);
        return request(endpoint, httpMethod, requestDatas, contentType, headers);
    },
    request

}