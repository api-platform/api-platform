import { combineReducers } from 'redux';

export function error(state = null, action) {
  switch (action.type) {
    case 'BOOK_DELETE_ERROR':
      return action.error;

    default:
      return state;
  }
}

export function loading(state = false, action) {
  switch (action.type) {
    case 'BOOK_DELETE_LOADING':
      return action.loading;

    default:
      return state;
  }
}

export function deleted(state = null, action) {
  switch (action.type) {
    case 'BOOK_DELETE_SUCCESS':
      return action.deleted;

    default:
      return state;
  }
}

export default combineReducers({ error, loading, deleted });
