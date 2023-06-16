import instance from "./api.service";

const updateSettings = async (settingsId, settings) => {
    await instance.post(`/settings/edit/${settingsId}`, settings);
}

const getFile = async (fileName: string) => {
    return await instance.get(`/file/display/${fileName}`);
}

const FileService = {
    getFile
};

export default FileService;