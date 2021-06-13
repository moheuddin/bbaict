import axios from 'axios';
import config from '../../config';
//console.log(config.API_URL)
const API_URL = config.API_URL;

class AuthService {
  login(user) {

    return axios
      .post(API_URL + 'login.php', {
        username: user.username,
        password: user.password
      })
      .then(response => {
        //console.log( JSON.stringify(response.data));
        if (response.data.jwt) {
          localStorage.setItem('user', JSON.stringify(response.data));
        }

        return response.data;
      });
  }

  logout() {
    localStorage.removeItem('user');
  }

  register(user) {
    return axios.post(API_URL + 'register.php', {
      username: user.username,
      email: user.email,
      password: user.password
    });
  }
}

export default new AuthService();
