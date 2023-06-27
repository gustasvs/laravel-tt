import axios from 'axios';

const save_log = (error, token) => {

    if (token) {
        axios.get(`http://localhost:2000/api/get_auth_user`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
          .then((response) => {          
            axios.post('http://localhost:2000/api/log', {
                error: error,
                userName: response.data.name,
            }, {
                headers: {
                Authorization: `Bearer ${token}`,
                },
            })
            .then((response) => {
            console.log('Log saved successfully:', response.data);
            })
            .catch((error) => {
            console.error('Failed to save log:', error);
            // Handle error if needed
            });
        })
    }
};

export default save_log;
