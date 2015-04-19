// Generated by Coffeescript
var Book, React, Table;

React = require('react');

React.Bootstrap = require('react-bootstrap');

Table = React.Bootstrap.Table;

Book = require('./Book');

module.exports = React.createClass({
  getDefaultProps: function() {
    return {
      books: [],
      handleGradelevelOfBookAssignmentsDelete: function() {
        return {};
      },
      handleGradeOfBookAssignmentsDelete: function() {
        return {};
      },
      handleBookAssignmentsDelete: function() {
        return {};
      }
    };
  },
  render: function() {
    return React.createElement(Table, {
      "bordered": true
    }, React.createElement("thead", null, React.createElement("tr", null, React.createElement("th", null, "Buch"), React.createElement("th", null, "Jahrgang"), React.createElement("th", null, "Klasse"))), this.props.books.map((function(_this) {
      return function(book) {
        return React.createElement(Book, {
          "key": book.id,
          "data": book,
          "handleGradelevelOfBookAssignmentsDelete": _this.props.handleGradelevelOfBookAssignmentsDelete,
          "handleGradeOfBookAssignmentsDelete": _this.props.handleGradeOfBookAssignmentsDelete,
          "handleBookAssignmentsDelete": _this.props.handleBookAssignmentsDelete
        });
      };
    })(this)));
  }
});
