import { Link, NavLink } from 'react-router-dom';
import './Navigation.scss';

function Navigation() {
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
                    <div className='action-container'>
                        Logout
                    </div>
                </div>  
            </div>
        </div>
    );
}

export default Navigation;