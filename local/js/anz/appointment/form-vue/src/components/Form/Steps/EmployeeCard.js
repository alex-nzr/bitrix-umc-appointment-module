import emptyPhotoImg from '../../../static/no-photo.svg'
import starImg from '../../../static/star.svg'
import './EmployeeCard.css'

export const EmployeeCard = {
    name: 'EmployeeCard',
    data(){
        return {
            photoPlaceHolder: emptyPhotoImg,
            starPicture: starImg,
            starCount: 5,
            rating: 4.8
        }
    },
    // language=Vue
    template:
        `
          <div class="appointment-form-employee-card">
            <div class="appointment-form-employee-card-photo">
              <img :src="photoPlaceHolder" alt="card">
            </div>
            <div class="appointment-form-employee-card-info">
              <div class="appointment-form-employee-card-info-text">
                <p>Бочарова Евгения Сергеевна</p>
                <span>врач оториноларинголог</span>
              </div>
              <div class="appointment-form-employee-card-rating">
                <div class="appointment-form-employee-card-rating-stars">
                  <img v-for="i in starCount" :key="i" :src="starPicture" alt="star">
                </div>
                <span class="points">{{rating}}</span>
              </div>
            </div>
          </div>
        `
}
