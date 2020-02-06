import { combineReducers } from 'redux';

export function error(state = null, action) {
  switch (action.type) {
    case 'BOOK_CREATE_ERROR':
      return action.error;

    default:
      return state;
  }
}

export function loading(state = false, action) {
  switch (action.type) {
    case 'BOOK_CREATE_LOADING':
      return action.loading;

    default:
      return state;
  }
}

export function created(state = null, action) {
  switch (action.type) {
    case 'BOOK_CREATE_SUCCESS':
      return action.created;

    default:
      return state;
  }
}

export default combineReducers({ error, loading, created });
