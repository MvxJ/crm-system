import { notification } from "../../node_modules/antd/es/index";
import instance from "./api";
import FormatUtils from "./format-utils";

const downloadPDF = async (fileName, action) => {
    try {
      const encodedFileName = encodeURIComponent(fileName);
      const downloadUrl = `/file/download?file=${encodedFileName}`;
      const { data } = await instance.get(downloadUrl, { responseType: 'blob', });

      if (action == 'open') {
        openPdfInNewTab(data);
      } else if (action == 'download') {
        downloadFile(data, fileName);
      }

    } catch (e) {
      notification.error({
        message: "There was an error while downloading file.",
        description: e.message,
        placement: "bottomRight",
        type: "error"
      })
    }
  }

  const openPdfInNewTab = (pdfContent) => {
    const blob = new Blob([pdfContent], { type: 'application/pdf' });
    const dataUrl = URL.createObjectURL(blob);
    window.open(dataUrl, '_blank');
  };

  const downloadFile = (pdfContent, fileName) => {
    const blob = new Blob([pdfContent], { type: 'application/pdf' });
    const dataUrl = URL.createObjectURL(blob);
    const anchor = document.createElement('a');

    anchor.href = dataUrl;
    anchor.download = FormatUtils.extractFileName(fileName);
    anchor.click();
  }

  const PdfUtils = {
    downloadPDF
  }

  export default PdfUtils;