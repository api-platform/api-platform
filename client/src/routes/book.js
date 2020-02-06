import React from 'react';
import { Route } from 'react-router-dom';
import { List, Create, Update, Show } from '../components/book/';

export default [
  <Route path="/books/create" component={Create} exact key="create" />,
  <Route path="/books/edit/:id" component={Update} exact key="update" />,
  <Route path="/books/show/:id" component={Show} exact key="show" />,
  <Route path="/books/" component={List} exact strict key="list" />,
  <Route path="/books/:page" component={List} exact strict key="page" />
];
