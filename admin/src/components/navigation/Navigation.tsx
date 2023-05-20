import { Link, NavLink, useNavigate } from 'react-router-dom';
import './Navigation.scss';
import AuthService from '../../service/auth.service';
import { FaUsers, FaUsersCog, FaThLarge, FaPowerOff } from 'react-icons/fa';

export interface INavigationProps {
    setIsAuthenticated: React.Dispatch<React.SetStateAction<boolean>>;
};

const Navigation: React.FunctionComponent<INavigationProps> = (props) => {
    const navigate = useNavigate();
    const logOut = ()  => {
        AuthService.logout();
        props.setIsAuthenticated(AuthService.isAuthenticated());
        navigate("/login");
    }

    return (
        <div className="side-navigation">
            <div className='style-container'>
                <div className="company-details">
                    <h3>CRM System</h3>
                </div>
                <div className="navigation-links">
                    <Link to={"/"} >
                        <div className='link-container'>
                            <span className='nav-el-icon'><FaThLarge /></span>Dashboard
                        </div>
                    </Link>
                    <Link to={"/users"} >
                        <div className='link-container'>
                            <span className='nav-el-icon'><FaUsersCog /></span> Users
                        </div>
                    </Link>
                    <Link to={"/customers"} >
                        <div className='link-container'>
                           <span className='nav-el-icon'><FaUsers /></span> Customers
                        </div>
                    </Link>
                </div>
                <div className="user-actions">
                    <div className='action-container' onClick={logOut}>
                        <span className='nav-el-icon'><FaPowerOff /></span>Logout
                    </div>
                </div>  
            </div>
        </div>
    );
}

export default Navigation;