const HelpButton = ({ Image, url, title }) => (
  <a
    target="_blank"
    rel="noopener noreferrer"
    href={url}
    className="help__circle"
    title={title}
  >
    <Image />
  </a>
);

export default HelpButton;
