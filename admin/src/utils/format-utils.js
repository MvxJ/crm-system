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

const formatHourWithMinutes = (dateString) => {
  const date = new Date(dateString);
  const options = {
    hour: '2-digit',
    minute: '2-digit',
  };

  const formattedTime = date.toLocaleString('en-US', options);

  return `${formattedTime}`;
};

const getMessageBadgeObj = (type) => {
  var obj = {
    text: '',
    color: ''
  }

  switch (type) {
    case 0:
      obj.text = 'Notification';
      obj.color = '#91d5ff';
      break;
    case 1:
      obj.text = 'Reminder';
      obj.color = '#ffd666';
      break
    case 2:
      obj.text = 'Message';
      obj.color = '#e6f7ff';
      break;
    case 3:
      obj.text = 'Account Confirmation';
      obj.color = '#91d5ff';
      break;
    case 4:
      obj.text = 'Two-Factor Authentication';
      obj.color = '#91d5ff';
      break;
  }

  return obj;
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

const getDeviceType = (type) => {
  var typeString = '';

  switch (type) {
    case 0:
      typeString = 'Router';
      break;
    case 1:
      typeString = 'Decoder';
      break;
    case 2:
      typeString = 'Phone';
      break;
    default:
      typeString = 'uknown';
      break;
  }

  return typeString;
}

const getPaymentStatusBadge = (status) => {
  const obj = { text: '', color: '' };

  switch (status) {
    case 0:
      obj.text = 'Pending';
      obj.color = '#69c0ff';
      break;
    case 1:
      obj.text = 'Posted';
      obj.color = '#95de64';
      break;
    default:
      obj.status = 'Uknown';
      obj.color = '#ffd666'
      break;
  }

  return obj;
}

const getPaidBy = (paidBy) => {
  var paidByString = '';

  switch (paidBy) {
    case 0:
      paidByString = 'Card'
      break;
    case 1:
      paidByString = 'BLIK'
      break;
    case 2:
      paidByString = 'Online Payments'
      break;
    case 3:
      paidByString = 'Cash'
      break;
    default:
      paidByString = 'Uknown'
      break;
  }

  return paidByString;
}

const getServiceRequestStatusBadge = (status) => {
  const statusObj = { text: '', color: '' }

  switch (status) {
    case 0:
      statusObj.text = 'Opened';
      statusObj.color = '#69c0ff'
      break;
    case 1:
      statusObj.text = 'Realization';
      statusObj.color = '#ffd666'
      break;
    case 2:
      statusObj.text = 'Closed';
      statusObj.color = '#95de64'
      break;
    case 3:
      statusObj.text = 'Cancelled';
      statusObj.color = '#ff7875'
      break;
    default:
      statusObj.text = 'Uknown';
      statusObj.color = '#8c8c8c'
      break;
  }

  return statusObj;
}

const getDeviceStatus = (status) => {
  const obj = {text: '', color: ''}

  switch (status) {
    case 0:
      obj.text = "AVILABLE";
      obj.color = "#95de64";
      break;
    case 1:
      obj.text = "RESERVED";
      obj.color = "#ffd666";
      break;
    case 2:
      obj.text = "DESTROYED";
      obj.color = "#ff7875";
      break;
    case 3:
      obj.text = "SOLD";
      obj.color = "#52c41a";
      break;
    case 4:
      obj.text = "RENTED";
      obj.color = "#40a9ff";
      break;
      case 5:
        obj.text = "DEFECTIVE";
        obj.color = "#ad6800";
        break;
  }

  return obj;
}

const getOfferDuration = (duration) => {
  var text = '';
  
  switch (duration) {
    case 0:
      text = '12 Months';
      break;
    case 1:
      text = '24 Months';
      break;
    default:
      text = 'No limit'
      break;
  }

  return text;
}

const getOfferType = (type) => {
  var text = '';

  switch (type) {
    case 0:
      text = 'Internet';
      break;
    case 1:
      text = 'Television';
      break;
    case 2:
      text = 'Internet + Television';
      break;
    default:
      text = 'Unknown';
      break;
  }

  return text;
}

const getOfferDiscount = (discount, discountType, price) => {
  if (discount == 0 || discount == null || discountType == null) {
    return '-';
  }

  if (discountType == 1) {
    return ((price * discount) / 100).toFixed(2);
  }

  return discount;
}

const getServiceVisitBadge = (isFinished, cancelled) => {
  const badge = {text: '', color: ''}

  if (isFinished == true) {
    badge.text = 'Finished';
    badge.color = '#95de64';
  } else if (cancelled == true) {
    badge.text = 'Cancelled';
    badge.color = '#ff7875';
  } else {
    badge.text = 'Open';
    badge.color = '#91d5ff';
  }

  return badge;
}

const FormatUtils = {
  formatDateWithTime,
  extractFileName,
  getBillBadgeDetails,
  getContractBadgeDetails,
  getMessageBadgeObj,
  getDeviceType,
  getPaidBy,
  getPaymentStatusBadge,
  getServiceRequestStatusBadge,
  getDeviceStatus,
  getOfferDuration,
  getOfferType,
  getOfferDiscount,
  getServiceVisitBadge,
  formatHourWithMinutes
}

export default FormatUtils;