import React, { useEffect, useState } from "react";
import instance from "../../../service/api.service";
import { useNavigate, useParams } from "react-router-dom";
import OfferService from "../../../service/offer.service";

export interface IOfferFormPageProps {};

const OfferForm: React.FunctionComponent<IOfferFormPageProps> = (props) => {
    const { id } = useParams();
    const [offer, setOffer] = useState(null);
    const [formData, setFormData] = useState(
        { 
            title: "", 
            description: "", 
            price: 0,
            download_speed: 0,
            upload_speed: 0,
            discount: 0,
            new_users: false
        }
    );
    const navigate = useNavigate();

    const fetchData = async () => {
        try {
            if (id) {
                const response = await instance.get(`/offer/${id}`);
                setOffer(response.data.offer);
                setFormData({
                    title: response.data.offer.title,
                    description: response.data.offer.description,
                    price: response.data.offer.price,
                    download_speed: response.data.offer.downloadSpeed,
                    discount: response.data.offer.percentageDiscount,
                    new_users: response.data.offer.forNewUsers,
                    upload_speed: response.data.offer.uploadSpeed
                });
            }
        } catch (error) {
            console.error("Error fetching data:", error);
            navigate("/offers")
        }
    };

    const handleInputChange = (event) => {
        const { name, value } = event.target;
        setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
    };

    const saveOffer = async () => {
        await OfferService.updateOffer(id, {
            title: formData.title,
            description: formData.description,
            price: parseInt(formData.price),
            download_speed: parseInt(formData.download_speed),
            upload_speed: parseInt(formData.upload_speed),
            discount: parseInt(formData.discount)
        });
        navigate(`/offers/detail/${id}`);
    }

    const addOffer = async () => {
        const response = await OfferService.addOffer({
            title: formData.title,
            description: formData.description,
            price: parseInt(formData.price),
            download_speed: parseInt(formData.download_speed),
            upload_speed: parseInt(formData.upload_speed),
            discount: parseInt(formData.discount)
        });
        
        if (response.data.status == 'success') {
            navigate(`/offers/detail/${response.data.offerId}`)
        }
    }

    useEffect(() => {
        fetchData();
      }, []
    );

    return (
        <div className="card">
            <div className="card-header">
                <div className="card-title">
                    { offer ? <h3>Edit #{offer.id} - {offer.title}</h3> : <h3>Add offer</h3>}
                </div>
                <div className="card-actions">
                    { offer == null? 
                        <button className="button-standard" onClick={addOffer}>
                            Add
                        </button>
                        :
                        <button className="button-standard" onClick={saveOffer}>
                            Save
                        </button>
                    }
                </div>
            </div>
            <div className="card-body">
                <form>
                    <div className="row">
                        <div className="col">
                            <label>Title:</label>
                            <input
                                type="text"
                                name="title"
                                value={formData.title}
                                onChange={handleInputChange}
                            />
                        </div>
                        <div className="col">
                            <label>Price:</label>
                            <input
                                type="text"
                                name="price"
                                value={formData.price}
                                onChange={handleInputChange}
                            />
                        </div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <label>Upload speed:</label>
                            <input
                                type="number"
                                name="upload_speed"
                                value={formData.upload_speed}
                                onChange={handleInputChange}
                            />
                        </div>
                        <div className="col">
                            <label>Download speed:</label>
                            <input
                                type="number"
                                name="download_speed"
                                value={formData.download_speed}
                                onChange={handleInputChange}
                            />
                        </div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <label>Discount:</label>
                            <input
                                type="text"
                                name="discount"
                                value={formData.discount}
                                onChange={handleInputChange}
                            />
                        </div>
                    </div>
                    <div className="row">
                        <div className="col col-100">
                            <label>Description:</label>
                            <textarea
                                name="description"
                                value={formData.description}
                                onChange={handleInputChange}
                            />
                        </div>
                    </div>

                </form>
            </div>
        </div>
    );
}

export default OfferForm;