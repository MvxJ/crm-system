import { Link, NavLink, useNavigate } from 'react-router-dom';
import './Navigation.scss';
import AuthService from '../../service/auth.service';

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
                            Dashboard
                        </div>
                    </Link>
                    <Link to={"/users"} >
                        <div className='link-container'>
                            Users
                        </div>
                    </Link>
                    <Link to={"/customers"} >
                        <div className='link-container'>
                            Customers
                        </div>
                    </Link>
                </div>
                <div className="user-actions">
                    <div className='action-container' onClick={logOut}>
                        Logout
                    </div>
                </div>  
            </div>
        </div>
    );
}

export default Navigation;