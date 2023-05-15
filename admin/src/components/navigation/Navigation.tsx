import './Navigation.scss';

function Navigation() {
    return (
        <div className="side-navigation">
            <div className='style-container'>
                <div className="company-details">
                    <h3>CRM System</h3>
                </div>
                <div className="navigation-links">
                    <ul>
                        <li>Dashboard</li>
                        <li>Users</li>
                    </ul>
                </div>
                <div className="user-actions">
                    <div>
                        Logout
                    </div>
                </div>  
            </div>
        </div>
    );
}

export default Navigation;