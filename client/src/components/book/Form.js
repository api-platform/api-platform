import React, { Component } from 'react';
import { Field, reduxForm } from 'redux-form';
import PropTypes from 'prop-types';

class Form extends Component {
  static propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    error: PropTypes.string
  };

  renderField = data => {
    data.input.className = 'form-control';

    const isInvalid = data.meta.touched && !!data.meta.error;
    if (isInvalid) {
      data.input.className += ' is-invalid';
      data.input['aria-invalid'] = true;
    }

    if (this.props.error && data.meta.touched && !data.meta.error) {
      data.input.className += ' is-valid';
    }

    return (
      <div className={`form-group`}>
        <label
          htmlFor={`book_${data.input.name}`}
          className="form-control-label"
        >
          {data.input.name}
        </label>
        <input
          {...data.input}
          type={data.type}
          step={data.step}
          required={data.required}
          placeholder={data.placeholder}
          id={`book_${data.input.name}`}
        />
        {isInvalid && <div className="invalid-feedback">{data.meta.error}</div>}
      </div>
    );
  };

  render() {
    return (
      <form onSubmit={this.props.handleSubmit}>
        <Field
          component={this.renderField}
          name="isbn"
          type="text"
          placeholder="The ISBN of this book (or null if doesn't have one)."
        />
        <Field
          component={this.renderField}
          name="title"
          type="text"
          placeholder="The title of this book."
        />
        <Field
          component={this.renderField}
          name="description"
          type="text"
          placeholder="The description of this book."
        />
        <Field
          component={this.renderField}
          name="author"
          type="text"
          placeholder="The author of this book."
        />
        <Field
          component={this.renderField}
          name="publicationDate"
          type="dateTime"
          placeholder="The publication date of this book."
        />
        <Field
          component={this.renderField}
          name="reviews"
          type="text"
          placeholder="Available reviews for this book."
          normalize={v => (v === '' ? [] : v.split(','))}
        />

        <button type="submit" className="btn btn-success">
          Submit
        </button>
      </form>
    );
  }
}

export default reduxForm({
  form: 'book',
  enableReinitialize: true,
  keepDirtyOnReinitialize: true
})(Form);
