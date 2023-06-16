import React, {useEffect, useState} from "react";
import instance from "../../service/api.service";
import SettingsService from "../../service/settings.service";
import FileService from "../../service/file.service";

export interface ISettingsPageProps {};

const Settings: React.FunctionComponent<ISettingsPageProps> = (props) => {
    const [settings, setSettings] = useState([]);
    const [selectedFile, setSelectedFile] = useState(null);
    const [logoName, setLogoName] = useState(null);
    const [previewImage, setPreviewImage] = useState(null);
    const [formData, setFormData] = useState(
        {
            companyName: "",
            companyAddress: "",
            companyPhoneNumber: "",
            emailAddress: "",
            facebookUrl: "",
            privacyPolicy: "",
            termsAndConditions: "",
            mailerAddress: "",
            technicalSupportNumber: ""
        }
    );

    const fetchData = async () => {
        try {
            const response = await instance.get(`/settings/1`);
            setSettings(response.data.settings);
            setFormData({
                companyName: response.data.settings.companyName,
                companyAddress: response.data.settings.companyAddress,
                companyPhoneNumber: response.data.settings.companyPhoneNumber,
                emailAddress: response.data.settings.emailAddress,
                facebookUrl: response.data.settings.facebookUrl,
                privacyPolicy: response.data.settings.privacyPolicy,
                termsAndConditions: response.data.settings.termsAndConditions,
                mailerAddress: response.data.settings.mailerAddress,
                technicalSupportNumber: response.data.settings.technicalSupportNumber
            });
            setLogoName(response.data.settings.logoUrl);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    useEffect(() => {
            fetchData();
        }, []
    );

    const saveSettings = async () => {
        await SettingsService.updateSettings(settings.id, {
            companyName: formData.companyName,
            companyAddress: formData.companyAddress,
            companyPhoneNumber: formData.companyPhoneNumber,
            emailAddress: formData.emailAddress,
            facebookUrl: formData.facebookUrl,
            privacyPolicy: formData.privacyPolicy,
            termsAndConditions: formData.termsAndConditions,
            mailerAddress: formData.mailerAddress,
            technicalSupportNumber: formData.technicalSupportNumber
        });

        if (selectedFile) {
            await SettingsService.uploadLogo(settings.id, selectedFile)
        }
    }

    const handleInputChange = (event) => {
        const { name, value } = event.target;
        setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
    };

    const handleFileChange = (event) => {
        const file = event.target.files[0];
        setSelectedFile(file);

        if (file) {
            const reader = new FileReader();
            reader.onloadend = () => {
                setPreviewImage(reader.result);
            };
            reader.readAsDataURL(file);
        } else {
            setPreviewImage(null);
        }
    };

    return (
        <div className="card">
            <div className="card-header">
                <div className="card-title">
                    <h3>Settings</h3>
                </div>
                <div className="card-actions">
                    <button className="button-standard" onClick={saveSettings}>
                        Save
                    </button>
                </div>
            </div>
            <div className="card-body">
                <form>
                    <h3>Basic settings</h3>
                    <div className="row">
                        <div className="col">
                            <label>Company name:</label>
                            <input
                                type="text"
                                name="companyName"
                                value={formData.companyName}
                                onChange={handleInputChange}
                            />
                        </div>
                        <div className="col">
                            <label>Address:</label>
                            <input
                                type="text"
                                name="companyAddress"
                                value={formData.companyAddress}
                                onChange={handleInputChange}
                            />
                        </div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <label>Phone number:</label>
                            <input
                                type="text"
                                name="companyPhoneNumber"
                                value={formData.companyPhoneNumber}
                                onChange={handleInputChange}
                            />
                        </div>
                        <div className="col">
                            <label>Email:</label>
                            <input
                                type="text"
                                name="emailAddress"
                                value={formData.emailAddress}
                                onChange={handleInputChange}
                            />
                        </div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <label>Facebook URL</label>
                            <input
                                type="text"
                                name="facebookUrl"
                                value={formData.facebookUrl}
                                onChange={handleInputChange}
                            />
                        </div>
                        <div className="col">
                            <label>Technical support number</label>
                            <input
                                type="text"
                                name="technicalSupportNumber"
                                value={formData.technicalSupportNumber}
                                onChange={handleInputChange}
                            />
                        </div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <label>Logo</label>
                            <input type="file" onChange={handleFileChange} />
                        </div>
                    </div>
                    <h3>Mailer settings</h3>
                    <div className="row">
                        <div className="col">
                            <label>Mailer address:</label>
                            <input
                                type="text"
                                name="mailerAddress"
                                value={formData.mailerAddress}
                                onChange={handleInputChange}
                            />
                        </div>
                    </div>
                    <h3>Privacy settings</h3>
                    <div className="row">
                        <div className="col col-100">
                            <label>Terms and conditions:</label>
                            <textarea
                                name="termsAndConditions"
                                value={formData.termsAndConditions}
                                onChange={handleInputChange}
                            />
                        </div>
                        { previewImage || logoName ?
                            <img src={previewImage ? previewImage : `http://localhost:8000/api/file/display/${logoName}`}  alt="system_logo" />
                            : null
                        }
                    </div>
                    <div className="row">
                        <div className="col col-100">
                            <label>Privacy policy:</label>
                            <textarea
                                name="privacyPolicy"
                                value={formData.privacyPolicy}
                                onChange={handleInputChange}
                            />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default Settings;