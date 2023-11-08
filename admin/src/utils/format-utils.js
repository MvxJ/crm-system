const extractFileName = (fileName) => {
    const lastIndex = fileName.lastIndexOf('/');
    var name = '';

    if (lastIndex !== -1) {
      name = fileName.substring(lastIndex + 1);
    } else {
      name = 'file.pdf'
    }

    return name;
}

const formatDateWithTime = (dateString) => {
    const date = new Date(dateString);
    const options = {
      weekday: 'long',
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
    };
  
    const formattedDate = date.toLocaleString('en-US', options);
  
    return `${formattedDate}`;
}

const getContractBadgeDetails = (status) => {
    var obj = {
      text: '',
      color: ''
    }

    switch (status) {
      case 0:
        obj.text = 'Awaiting Activation';
        obj.color = '#69c0ff';
        break;
      case 1:
        obj.text = 'Closed';
        obj.color = '#f5222d';
        break
      case 2:
        obj.text = 'Active';
        obj.color = '#95de64';
        break;
        case 3:
          obj.text = 'Inactive';
          obj.color = '#ffd666';
          break; 
    }

    return obj;
  }

const getBillBadgeDetails = (status) => {
    var obj = {
      text: '',
      color: ''
    }

    switch (status) {
      case 0:
        obj.text = 'Awaiting Payment';
        obj.color = '#69c0ff';
        break;
      case 1:
        obj.text = 'Paid';
        obj.color = '#95de64';
        break;
      case 2:
        obj.text = 'Partially Paid';
        obj.color = '#ffd666';
        break
      case 3:
        obj.text = 'Payment Delayed';
        obj.color = '#f5222d';
        break;
      case 4:
        obj.text = 'Not Paid';
        obj.color = '#f5222d';
        break;
      case 5:
        obj.text = 'Refunded';
        obj.color = '#69c0ff';
        break;  
    }

    return obj;
  }

const FormatUtils = {
    formatDateWithTime,
    extractFileName,
    getBillBadgeDetails,
    getContractBadgeDetails
}

export default FormatUtils;