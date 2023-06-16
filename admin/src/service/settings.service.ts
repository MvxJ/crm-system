import instance from "./api.service";

const updateSettings = async (settingsId, settings) => {
    await instance.post(`/settings/edit/${settingsId}`, settings);
}

const uploadLogo = async (settingsId, file) => {
    const formData = new FormData();
    formData.append('logo', file);

    await instance.post(`/settings/${settingsId}/logo/upload`, formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    });
}

const SettingsService = {
    updateSettings,
    uploadLogo
};

export default SettingsService;