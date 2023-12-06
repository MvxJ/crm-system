import './SharedStyles.css'
import protectedImage from './../assets/images/sign-in.png'
import MainCard from './MainCard';
import { useNavigate } from '../../node_modules/react-router-dom/dist/index';

const AccessError = ({neededRole}) => {
    const navigate = useNavigate();

    const goBack = () => {
      navigate(-2);
    };
  

return (
<>
      <MainCard style={{ height: "90vh" }}>
        <div className="wrapper">
          <div className="image-container">
            <img src={protectedImage} alt="protected_route"/>
          </div>
          <div className="text-container">
            <div style={{}}>
              It seems like you are trying to access page that requaire role of "<span className='roleName'>{neededRole ? neededRole : 'Access to Web Dashboard'}</span>"
            </div>
            <div>
              Please go back to last visited{" "}
              <a onClick={goBack} className="link">
                page
              </a>{" "}
              or Contact with Adminsitrator
            </div>
          </div>
        </div>
      </MainCard>
    </>
);
};

export default AccessError;
