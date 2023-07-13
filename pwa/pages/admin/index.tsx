import dynamic from 'next/dynamic';

const Admin = dynamic(() => import('../../components/admin/Admin'), {
  ssr: false,
});

const AdminPage = () => (
  <>
    <Admin />
    <style jsx global>
      {`
        body {
          margin: 0;
          padding: 0;
          font-family: sans-serif;
        }
      `}
    </style>
  </>
);
export default AdminPage;
