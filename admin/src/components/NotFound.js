import { useNavigate } from "../../node_modules/react-router-dom/dist/index";
import MainCard from "./MainCard";
import "./SharedStyles.css";
import error404 from './../assets/images/404-error.png'

const NotFound = () => {
  const navigate = useNavigate();

  const goBack = () => {
    navigate(-1);
  };

  const goHome = () => {
    navigate("/");
  };

  return (
    <>
      <MainCard style={{ height: "90vh" }}>
        <div className="wrapper">
          <div className="image-container">
            <img src={error404} alt="not_found_image"/>
          </div>
          <div className="text-container">
            <div style={{}}>
              It seems like we can't find the page you are looking for...
            </div>
            <div>
              Please go back to last visited{" "}
              <a onClick={goBack} className="link">
                page
              </a>{" "}
              or{" "}
              <a onClick={goHome} className="link">
                Home page
              </a>
            </div>
          </div>
        </div>
      </MainCard>
    </>
  );
};

export default NotFound;
