import { useNavigate } from 'react-router-dom';
import './NotFound.scss';
import notFoundImage from '../../assetts/404-error.png';

function NotFound() {
    const navigate = useNavigate();
    const redirectToDashboard = () => {
        navigate("/");
    }

    return (
        <div className='card'>
            <div className='not-found-container'>
                <div className='not-found-text'>
                    <p>The page you are trying to access was not found.<br />Please visit <a onClick={redirectToDashboard}>Dashboard</a></p>
                </div>
                <div className='not-dound-image'>
                    <img src={notFoundImage} alt="not-found-image" />
                </div>
            </div>
        </div>
    );
}

export default NotFound;