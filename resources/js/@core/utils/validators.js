import { isEmpty, isEmptyArray, isNullOrUndefined } from './helpers'

// 👉 Required Validator
export const requiredValidator = value => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return 'This field is required'
  
  return !!String(value).trim().length || 'This field is required'
}

// 👉 Email Validator
export const emailValidator = value => {
  if (isEmpty(value))
    return true
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  if (Array.isArray(value))
    return value.every(val => re.test(String(val))) || 'The Email field must be a valid email'
  
  return re.test(String(value)) || 'The Email field must be a valid email'
}

// 👉 Password Validator
export const passwordValidator = password => {
  const regExp = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*()]).{8,}/
  const validPassword = regExp.test(password)
  
  return validPassword || 'Field must contain at least one uppercase, lowercase, special character and digit with min 8 chars'
}

// 👉 Confirm Password Validator
export const confirmedValidator = (value, target) => value === target || 'The Confirm Password field confirmation does not match'

// 👉 Between Validator
export const betweenValidator = (value, min, max) => {
  const valueAsNumber = Number(value)
  
  return (Number(min) <= valueAsNumber && Number(max) >= valueAsNumber) || `Enter number between ${min} and ${max}`
}

// 👉 Integer Validator
export const integerValidator = value => {
  if (isEmpty(value))
    return true
  if (Array.isArray(value))
    return value.every(val => /^-?[0-9]+$/.test(String(val))) || 'This field must be an integer'
  
  return /^-?[0-9]+$/.test(String(value)) || 'This field must be an integer'
}

// 👉 Regex Validator
export const regexValidator = (value, regex) => {
  if (isEmpty(value))
    return true
  let regeX = regex
  if (typeof regeX === 'string')
    regeX = new RegExp(regeX)
  if (Array.isArray(value))
    return value.every(val => regexValidator(val, regeX))
  
  return regeX.test(String(value)) || 'The Regex field format is invalid'
}

// 👉 Alpha Validator
export const alphaValidator = value => {
  if (isEmpty(value))
    return true
  
  return /^[A-Z]*$/i.test(String(value)) || 'The Alpha field may only contain alphabetic characters'
}

// 👉 URL Validator
export const urlValidator = value => {
  if (isEmpty(value))
    return true
  const re = /^(https?):\/\/[^\s$.?#].[^\s]*$/
  
  return re.test(String(value)) || 'URL is invalid'
}

// 👉 Length Validator
export const lengthValidator = (value, length) => {
  if (isEmpty(value))
    return true
  
  return String(value).length === length || `The Min Character field must be at least ${length} characters`
}

// 👉 Alpha-dash Validator
export const alphaDashValidator = value => {
  if (isEmpty(value))
    return true
  const valueAsString = String(value)
  
  return /^[0-9A-Z_-]*$/i.test(valueAsString) || 'All Character are not valid'
}
export const validUSAPhone = value => {
  if (isEmpty(value))
    return true
  const valueAsString = String(value)
  
  return /^\(\d{3}\)\s\d{3}-\d{4}$/i.test(valueAsString) || 'Phone are not valid'
}
export const requiredPhone = value => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return 'Phone is required'
  
  return !!String(value).trim().length || ' Phone  is required'
}
export const requiredFirstName = value => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return 'First Name field is required'
  
  return !!String(value).trim().length || 'Name is required'
}
export const requiredName = value => {
if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
  return 'Name field is required'

return !!String(value).trim().length || 'Name is required'
}
export const requiredLastName = value => {
if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
  return 'Last Name field is required'

return !!String(value).trim().length || ' Last Name is required'
}
export const requiredEmail = value => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return 'Email field is required'
  
  return !!String(value).trim().length || 'Email is required'
}
export const requiredImageValidator = value => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return 'Image field is required'
  
  return !!String(value).trim().length || 'Email is required'
}
export const requiredExcelValidator = value => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return 'Excel field is required'
  
  return !!String(value).trim().length || 'Email is required'
}
export const requiredAmountFloat = (value) => {
  // Check if the value is null, undefined, an empty array, or false
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false) {
    return 'Amount field is required';
  }

  // Check if the value is a valid float number
  if (!isFloat(value)) {
    return 'Please enter a valid amount';
  }

  // Additional checks as needed...

  // Return true if the value passes validation
  return true;
};




// Check if a value is a valid float number
const isFloat = (value) => {
  if (typeof value !== 'number' && typeof value !== 'string') {
    return false;
  }
  // Use regex to match a valid float number (including negatives and decimals)
  return /^-?\d*\.?\d+$/.test(value);
};
export const requiredState = value => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return 'State field is required'
  
  return !!String(value).trim().length || 'State is required'
}
export const requiredAddress = value => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return  'Address is required'
  
  return !!String(value).trim().length || 'Address is required'
}
export const requiredGender = (value) => !!value || 'Gender is required'
export const requiredLicenseNumber = (value) => !!value || 'Medical License Number is required'
export const requiredYearsofExperience = (value) => !!value || 'Years of Experience is required'
export const requiredSpecialty = (value) => !!value || 'Practice or Provider of Specialty is required'

export const requiredZip = (value) => !!value || 'Zip Code is required'
export const requiredCity = value => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return 'City is required'
  
  return !!String(value).trim().length || 'City is required'
}
export const requiredPassword = value => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return 'Password field is required'
  
  return !!String(value).trim().length || 'Password field is required'
}
export const formatPrice = (price, currency = 'USD', locale = 'en-US') => {
  return new Intl.NumberFormat(locale, {
    style: 'currency',
    currency: currency,
  }).format(price);
};
export const expiryValidator = value =>  {
  // Check if the format is MM/YY
  const formatRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
  if (!formatRegex.test(value)) {
    return 'Invalid date format. Please use MM/YY';
  }

  // Check if the date is not expired (assuming the current date is 01/24 for example)
  const currentDate = new Date();
  const currentYear = currentDate.getFullYear() % 100;
  const currentMonth = currentDate.getMonth() + 1;

  const [inputMonth, inputYear] = value.split('/').map(Number);

  if (inputYear < currentYear || (inputYear === currentYear && inputMonth < currentMonth)) {
    return 'The card has expired';
  }

  return true;
};
export const cvvValidator = value => {
  return /^\d{3}$/.test(value) || 'Must be a 3-digit number';
};
export const cardNumberValidator = value => {
  // Adjust the regex based on your credit card number pattern
  const cardNumberPattern = /^(\d{15}|\d{16})$/;

  return cardNumberPattern.test(value) || 'Invalid credit card number';
};
